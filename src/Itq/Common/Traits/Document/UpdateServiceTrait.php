<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Document;

use Exception;
use Itq\Common\Exception as CommonException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait UpdateServiceTrait
{
    /**
     * Return the specified document.
     *
     * @param mixed $id
     * @param array $fields
     * @param array $options
     *
     * @return mixed
     */
    abstract public function get($id, $fields = [], $options = []);
    /**
     * @param string $fieldName
     * @param mixed  $fieldValue
     * @param array  $fields
     * @param array  $options
     *
     * @return mixed
     */
    abstract public function getBy($fieldName, $fieldValue, $fields = [], $options = []);
    /**
     * @param string $id
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     */
    public function update($id, $data, $options = [])
    {
        list($doc, $array, $old, $transitions) = $this->prepareUpdate($id, $data, $options);

        unset($data);

        $this->saveUpdate($id, $this->enrichUpdates($array, $doc, $options), $options);

        return $this->completeUpdate($id, $doc, $array, $old, $transitions, $options);
    }
    /**
     * @param mixed $fieldName
     * @param mixed $fieldValue
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function updateBy($fieldName, $fieldValue, $data, $options = [])
    {
        return $this->update($this->getBy($fieldName, $fieldValue, ['id'], $options)->id, $data, $options);
    }
    /**
     * @param array $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function updateBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $docs        = [];
        $arrays      = [];
        $olds        = [];
        $transitions = [];
        $idMatch     = [];
        $docIds      = [];
        $errors      = ['prepare' => [], 'complete' => [], 'saved' => null];

        foreach ($bulkData as $i => $data) {
            $id = $data['id'];
            unset($data['id']);
            try {
                list($docs[$i], $arrays[$id], $olds[$i], $transitions[$i]) = $this->prepareUpdate($id, $data, $options);
                $arrays[$id]  = $this->enrichUpdates($arrays[$id], $docs[$i], $options);
                $docIds[$i]   = $id;
                $idMatch[$id] = $i;
            } catch (Exception $e) {
                $errors['prepare'][$i] = ['data' => $data, 'exception' => $e];
            }
            unset($bulkData[$i]);
        }

        unset($bulkData);

        $saved = [];

        if (count($arrays)) {
            try {
                $this->saveUpdateBulk($arrays, $options);
                $saved = $arrays;
            } catch (Exception $e) {
                $errors['saved'] = ['exception' => $e];
                $saved = [];
            }
        }

        $completedDocs   = [];
        $completedDocsId = [];

        foreach ($saved as $id => $array) {
            unset($arrays[$id]);
            $i = $idMatch[$id];
            try {
                $completedDocs[$i] = $this->completeUpdate($id, $docs[$i], $array, $olds[$i], $transitions[$i], $options);
                $completedDocsId[$i] = $id;
                unset($docs[$i]);
            } catch (Exception $e) {
                $errors['complete'][$i] = ['doc' => $docs[$i], 'data' => $array, 'exception' => $e, 'id' => $id];
            }
            unset($olds[$i], $transitions[$i]);
        }

        $exceptions = [];
        $failedDocs = [];

        foreach ($errors['prepare'] as $i => $error) {
            $exceptions[$i] = $error['exception'];
            $failedDocs[$i] = $error['data'];
        }
        if (null !== $errors['saved']) {
            $exceptions['.saved'] = $errors['saved']['exception'];
        }
        foreach ($errors['complete'] as $i => $error) {
            $exceptions[$i] = $error['exception'];
            $failedDocs[$i] = $error['id'];
        }
        if (count($exceptions)) {
            throw new CommonException\BulkException($exceptions, $failedDocs, $completedDocsId);
        }

        unset($olds);
        unset($arrays);

        return (isset($options['returnId']) && true === $options['returnId']) ? $completedDocsId : $completedDocs;
    }
    /**
     * Increment the specified property of the specified document.
     *
     * @param mixed        $id
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function increment($id, $property, $value = 1, $options = [])
    {
        if (is_array($property)) {
            $this->saveIncrementProperties($id, $property, $options);
        } else {
            $this->saveIncrementProperty($id, $property, $value, $options);
        }

        return $this;
    }
    /**
     * Increment the specified property of the specified document.
     *
     * @param string       $fieldName
     * @param mixed        $fieldValue
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function incrementBy($fieldName, $fieldValue, $property, $value = 1, $options = [])
    {
        if (is_array($property)) {
            $this->saveIncrementProperties([$fieldName => $fieldValue], $property, $options);
        } else {
            $this->saveIncrementProperty([$fieldName => $fieldValue], $property, $value, $options);
        }

        return $this;
    }
    /**
     * Decrement the specified property of the specified document.
     *
     * @param string       $fieldName
     * @param mixed        $fieldValue
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function decrementBy($fieldName, $fieldValue, $property, $value = 1, $options = [])
    {
        return $this->incrementBy($fieldName, $fieldValue, $property, -$value, $options);
    }
    /**
     * Decrement the specified property of the specified document.
     *
     * @param mixed        $id
     * @param string|array $property
     * @param int          $value
     * @param array        $options
     *
     * @return $this
     */
    public function decrement($id, $property, $value = 1, $options = [])
    {
        if (is_array($property)) {
            $this->saveDecrementProperties($id, $property, $options);
        } else {
            $this->saveDecrementProperty($id, $property, $value, $options);
        }

        return $this;
    }
    /**
     * @param mixed $id
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    protected function prepareUpdate($id, $data = [], $options = [])
    {
        $options += ['id' => $id];

        $doc                           = $this->validateData($data, 'update', ['clearMissing' => false] + $options);
        $old                           = null;
        $hasWorkflows                  = false;
        $activeWorkflowsRequiredFields = [];

        if ($this->hasActiveWorkflows($doc, $options)) {
            $hasWorkflows = true;
            $activeWorkflowsRequiredFields = $this->getActiveWorkflowsRequiredFields($doc, $options);
        }

        if (true === $hasWorkflows || $this->observed('updated_old') || $this->observed('updated_full_old')) {
            $old = $this->get($id, array_unique(array_merge($activeWorkflowsRequiredFields, array_keys($data))), $options);
        }

        unset($data, $activeWorkflowsRequiredFields);

        $doc = $this->refreshModel($doc, ['operation' => 'update', 'populateNulls' => false, 'id' => $id] + $options);

        $this->applyBusinessRules('update', $doc, $options);

        $transitions = [];

        if ($hasWorkflows) {
            $transitions = $this->applyActiveWorkflows($doc, $old, $options);
            if (is_array($transitions)) {
                foreach ($transitions as $transition) {
                    $this->applyBusinessRules('update.'.$transition, $doc, $options);
                }
            }
        }

        return [$doc, $this->convertToArray($doc, $options), $old, $transitions];
    }
    /**
     * @param mixed $id
     * @param mixed $doc
     * @param array $array
     * @param mixed $old
     * @param array $transitions
     * @param array $options
     *
     * @return mixed
     */
    protected function completeUpdate($id, $doc, $array, $old, $transitions = [], $options = [])
    {
        $options += ['id' => $id];

        if (property_exists($doc, 'id') && null === $doc->id) {
            $doc->id = (string) $id;
        }

        unset($array);

        $doc = $this->cleanModel($doc, ['operation' => 'update'] + $options);

        $this->applyBusinessRules('complete_update', $doc, $options);

        foreach ($transitions as $transition) {
            $this->applyBusinessRules('complete_update.'.$transition, $doc, $options);
        }

        $this->event('updated', $doc);

        foreach ($transitions as $transition) {
            $this->event('updated.'.$transition, $doc);
        }

        unset($old);

        return $doc;
    }
    /**
     * @param string $id
     * @param array  $array
     * @param array  $options
     *
     * @return mixed|void
     */
    abstract protected function saveUpdate($id, array $array, array $options);
    /**
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    abstract protected function saveUpdateBulk(array $arrays, array $options);
    /**
     * @param array  $data
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    abstract protected function enrichUpdates($data, $class, array $options = []);
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
     * @param string|array $id
     * @param array        $properties
     * @param array        $options
     */
    abstract protected function saveIncrementProperties($id, $properties, array $options);
    /**
     * @param string|array $id
     * @param string       $property
     * @param mixed        $value
     * @param array        $options
     */
    abstract protected function saveIncrementProperty($id, $property, $value, array $options);
    /**
     * @param string|array $id
     * @param array        $properties
     * @param array        $options
     */
    abstract protected function saveDecrementProperties($id, array $properties, array $options);
    /**
     * @param string|array $id
     * @param string       $property
     * @param mixed        $value
     * @param array        $options
     */
    abstract protected function saveDecrementProperty($id, $property, $value, array $options);
    /**
     * @param string $mode
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     */
    abstract protected function validateData(array $data = [], $mode = 'create', array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return bool
     */
    abstract protected function hasActiveWorkflows($model, array $options = []);
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return bool
     */
    abstract protected function getActiveWorkflowsRequiredFields($model, array $options = []);
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
     * @param string $operation
     * @param mixed  $model
     * @param array  $options
     *
     * @return $this
     */
    abstract protected function applyBusinessRules($operation, $model, array $options = []);
    /**
     * @param mixed $model
     * @param mixed $previousModel
     * @param array $options
     *
     * @return array
     */
    abstract protected function applyActiveWorkflows($model, $previousModel, array $options = []);
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
     * @param string $event
     * @param mixed  $data
     *
     * @return $this
     */
    abstract protected function event($event, $data = null);
}
