<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\SubDocument;

use Exception;

/**
 * Update service trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait UpdateServiceTrait
{
    /**
     * @param string   $parentId
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return array
     */
    abstract public function find($parentId, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = []);
    /**
     * @return array
     */
    abstract public function getTypes();
    /**
     * @param string $parentId
     * @param string $id
     * @param array  $data
     * @param array  $options
     *
     * @return $this
     */
    public function update($parentId, $id, $data, $options = [])
    {
        list($doc, $array, $old, $transitions) = $this->prepareUpdate($parentId, $id, $data, $options);

        unset($data);

        $this->saveUpdate($parentId, $id, $array, $options);

        return $this->completeUpdate($parentId, $id, $doc, $array, $old, $transitions, $options);
    }
    /**
     * @param mixed $parentId
     * @param mixed $fieldName
     * @param mixed $fieldValue
     * @param array $data
     * @param array $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function updateBy($parentId, $fieldName, $fieldValue, $data, $options = [])
    {
        $docs = $this->find($parentId, [$fieldName => $fieldValue], ['id'], 1, 0, $options);

        if (!count($docs)) {
            throw $this->createNotFoundException("Unknown %s with %s '%s' (%s)", join(' ', $this->getTypes()), $fieldName, $fieldValue, $parentId);
        }

        return $this->update($parentId, array_shift($docs)->id, $data, $options);
    }
    /**
     * @param string $parentId
     * @param array  $bulkData
     * @param array  $options
     *
     * @return mixed
     */
    public function updateBulk($parentId, $bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $docs        = [];
        $changes     = [];
        $olds        = [];
        $arrays      = [];
        $transitions = [];

        foreach ($bulkData as $i => $data) {
            $id = $data['id'];
            unset($data['id']);
            list($docs[$i], $arrays[$i], $olds[$i], $transitions[$i]) = $this->prepareUpdate($parentId, $id, $data, $options);
            unset($bulkData[$i]);
            $this->pushUpdateInBulk($changes, $arrays[$i], $id);
        }

        $this->saveUpdateBulk($parentId, $changes, $options);

        foreach ($arrays as $i => $array) {
            $this->completeUpdate($parentId, $i, $docs[$i], $array, $olds[$i], $transitions[$i], $options);
            unset($arrays[$i], $olds[$i], $transitions[$i]);
        }

        unset($olds);
        unset($arrays);

        return $docs;
    }
    /**
     * Increment the specified property of the specified document.
     *
     * @param string       $parentId
     * @param mixed        $id
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function increment($parentId, $id, $property, $value = 1, $options = [])
    {
        $this->saveIncrementProperty($parentId, $id, $property, $value, $options);

        return $this;
    }
    /**
     * Decrement the specified property of the specified document.
     *
     * @param string       $parentId
     * @param mixed        $id
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function decrement($parentId, $id, $property, $value = 1, $options = [])
    {
        $this->saveDecrementProperty($parentId, $id, $property, $value, $options);

        return $this;
    }
    /**
     * @param string $parentId
     * @param mixed  $id
     * @param array  $data
     * @param array  $options
     *
     * @return array
     */
    protected function prepareUpdate($parentId, $id, $data = [], $options = [])
    {
        $options += ['id' => $id, 'parentId' => $parentId];

        $doc                           = $this->validateData($data, 'update', ['clearMissing' => false] + $options);
        $old                           = null;
        $hasWorkflows                  = false;
        $activeWorkflowsRequiredFields = [];

        if ($this->hasActiveWorkflows($parentId, $doc, $options)) {
            $hasWorkflows = true;
            $activeWorkflowsRequiredFields = $this->getActiveWorkflowsRequiredFields($parentId, $doc, $options);
        }

        if (true === $hasWorkflows || $this->observed('updated_old') || $this->observed('updated_full_old')) {
            $old = $this->get($parentId, $id, array_unique(array_merge($activeWorkflowsRequiredFields, array_keys($data))), $options);
        }

        unset($data, $activeWorkflowsRequiredFields);

        $doc = $this->refreshModel($doc, ['operation' => 'update', 'populateNulls' => false, 'parentId' => $parentId, 'id' => $id] + $options);

        $this->applyBusinessRules($parentId, 'update', $doc, $options);

        $transitions = [];

        if ($hasWorkflows) {
            $transitions = $this->applyActiveWorkflows($parentId, $doc, $old, $options);
        }

        return [$doc, $this->convertToArray($doc, $options), $old, $transitions];
    }
    /**
     * @param string $parentId
     * @param mixed  $id
     * @param mixed  $doc
     * @param array  $array
     * @param mixed  $old
     * @param array  $transitions
     * @param array  $options
     *
     * @return mixed
     */
    protected function completeUpdate($parentId, $id, $doc, $array, $old, $transitions = [], $options = [])
    {
        $options += ['id' => $id, 'parentId' => $parentId];

        if (property_exists($doc, 'id') && null === $doc->id) {
            $doc->id = (string) $id;
        }

        unset($array);

        $doc = $this->cleanModel($doc, ['operation' => 'update', 'parentId' => $parentId] + $options);

        $this->applyBusinessRules($parentId, 'complete_update', $doc, $options);

        foreach ($transitions as $transition) {
            $this->applyBusinessRules($parentId, 'complete_update.'.$transition, $doc, $options);
        }

        $this->event($parentId, 'updated', $doc);

        foreach ($transitions as $transition) {
            $this->event($parentId, 'updated.'.$transition, $doc);
        }

        unset($old);

        return $doc;
    }
    /**
     * @param mixed $parentId
     * @param mixed $id
     * @param array $array
     * @param array $options
     */
    abstract protected function saveUpdate($parentId, $id, array $array, array $options);
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    abstract protected function createNotFoundException($msg, ...$params);
    /**
     * @param mixed $bulkData
     * @param array $options
     *
     * @return $this
     *
     * @throws Exception
     */
    abstract protected function checkBulkData($bulkData, $options = []);
    /**
     * @param array $arrays
     * @param array $array
     * @param mixed $id
     */
    abstract protected function pushUpdateInBulk(&$arrays, $array, $id);
    /**
     * @param mixed $parentId
     * @param array $arrays
     * @param array $options
     */
    abstract protected function saveUpdateBulk($parentId, array $arrays, array $options);
    /**
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     */
    abstract protected function saveIncrementProperty($parentId, $id, $property, $value, array $options);
    /**
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     */
    abstract protected function saveDecrementProperty($parentId, $id, $property, $value, array $options);
    /**
     * @param string $mode
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     */
    abstract protected function validateData(array $data = [], $mode = 'create', array $options = []);
    /**
     * @param string $parentId
     * @param mixed  $model
     * @param array  $options
     *
     * @return bool
     */
    abstract protected function hasActiveWorkflows($parentId, $model, array $options = []);
    /**
     * @param string $parentId
     * @param mixed  $model
     * @param array  $options
     *
     * @return bool
     */
    abstract protected function getActiveWorkflowsRequiredFields($parentId, $model, array $options = []);
    /**
     * @param string $event
     *
     * @return bool
     */
    abstract protected function observed($event);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    abstract protected function refreshModel($model, array $options = []);
    /**
     * @param string $parentId
     * @param string $operation
     * @param mixed  $model
     * @param array  $options
     *
     * @return $this
     */
    abstract protected function applyBusinessRules($parentId, $operation, $model, array $options = []);
    /**
     * @param string $parentId
     * @param mixed  $model
     * @param mixed  $previousModel
     * @param array  $options
     *
     * @return array
     */
    abstract protected function applyActiveWorkflows($parentId, $model, $previousModel, array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return array
     */
    abstract protected function convertToArray($model, array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    abstract protected function cleanModel($model, array $options = []);
    /**
     * @param mixed  $parentId
     * @param string $event
     * @param mixed  $data
     *
     * @return $this
     */
    abstract protected function event($parentId, $event, $data = null);
}
