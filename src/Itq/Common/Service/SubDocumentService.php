<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Event\DocumentEvent;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SubDocumentService implements SubDocumentServiceInterface
{
    use Traits\ModelServiceTrait;
    use Traits\SubDocument\HelperTrait;
    use Traits\SubDocument\CreateServiceTrait;
    /**
     * Return the property of the specified document.
     *
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     */
    public function getProperty($parentId, $id, $property, $options = [])
    {
        return $this->convertToModelProperty($this->getRepository()->getProperty($parentId, $this->getRepoKey([$id, $property]), $options), $property, ['operation' => 'retrieve']);
    }
    /**
     * Return the property of the specified document if exist or default value otherwise.
     *
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param mixed  $defaultValue
     * @param array  $options
     *
     * @return mixed
     */
    public function getPropertyIfExist($parentId, $id, $property, $defaultValue = null, $options = [])
    {
        return $this->getRepository()->getPropertyIfExist($parentId, $this->getRepoKey([$id, $property]), $defaultValue, $options);
    }
    /**
     * Test if specified document exist.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return bool
     */
    public function has($parentId, $id, $options = [])
    {
        return $this->getRepository()->hasProperty($parentId, $this->getRepoKey([$id]), $options);
    }
    /**
     * Test if specified document exist by specified field.
     *
     * @param string $parentId
     * @param string $fieldName
     * @param mixed  $fieldValue
     * @param array  $options
     *
     * @return bool
     */
    public function hasBy($parentId, $fieldName, $fieldValue, $options = [])
    {
        return 0 < count($this->find($parentId, [$fieldName => $fieldValue], ['id'], 1, 0, $options));
    }
    /**
     * Test if specified document does not exist.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return bool
     */
    public function hasNot($parentId, $id, $options = [])
    {
        return !$this->has($parentId, $id, $options);
    }
    /**
     * Return the specified document.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $fields
     * @param array  $options
     *
     * @return mixed
     */
    public function get($parentId, $id, $fields = [], $options = [])
    {
        $fetchedFields = $this->prepareFields($fields);

        return $this->convertToModel(
            $this->getRepository()->getHashProperty($parentId, $this->getRepoKey([$id], $options + ['fieldMapping' => ['parentToken' => 'token']]), $fetchedFields, $options),
            ['requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'parentId' => $parentId, 'operation' => 'retrieve'] + $options
        );
    }
    /**
     * Check if specified document exist.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkExist($parentId, $id, $options = [])
    {
        $this->getRepository()->checkPropertyExist($parentId, $this->getRepoKey([$id], $options), $options);

        return $this;
    }
    /**
     * Check is specified document does not exist.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkNotExist($parentId, $id, $options = [])
    {
        $this->getRepository()->checkPropertyNotExist($parentId, $this->getRepoKey([$id], $options), $options);

        return $this;
    }
    /**
     * Count documents matching the specified criteria.
     *
     * @param string $parentId
     * @param mixed  $criteria
     * @param array  $options
     *
     * @return mixed
     */
    public function count($parentId, $criteria = [], $options = [])
    {
        if (!$this->getRepository()->hasProperty($parentId, $this->getRepoKey(), $options)) {
            return 0;
        }

        $items = $this->getRepository()->getListProperty($parentId, $this->getRepoKey(), $options);

        $this->filterItems($items, $criteria);

        unset($criteria);

        return count($items);
    }
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param string   $parentId
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return mixed
     */
    public function find($parentId, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        if (!$this->getRepository()->hasProperty($parentId, $this->getRepoKey())) {
            return [];
        }

        $items         = $this->getRepository()->getListProperty($parentId, $this->getRepoKey());
        $fetchedFields = $this->prepareFields($fields);

        $this->sortItems($items, $sorts, $options);
        $this->filterItems($items, $criteria, $fetchedFields, null, $options);
        $this->paginateItems($items, $limit, $offset, $options);

        foreach ($items as $k => $v) {
            $items[$k] = $this->convertToModel($v, ['requestedFields' => $fields, 'fetchedFields' => $fetchedFields, 'parentId' => $parentId, 'operation' => 'retrieve'] + $options);
        }

        return $items;
    }
    /**
     * @param mixed $parentId
     * @param array $criteria
     * @param array $fields
     * @param int   $offset
     * @param array $sorts
     * @param array $options
     *
     * @return mixed|null
     */
    public function findOne($parentId, $criteria = [], $fields = [], $offset = 0, $sorts = [], $options = [])
    {
        $items = $this->find($parentId, $criteria, $fields, 1, $offset, $sorts, $options);

        if (!count($items)) {
            return null;
        }

        return array_shift($items);
    }
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param mixed    $fieldName
     * @param mixed    $fieldValue
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return mixed
     */
    public function findBy($fieldName, $fieldValue, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        return $this->find(
            $this->getRepository()->getBy($fieldName, $fieldValue, ['_id'])['_id'],
            $criteria,
            $fields,
            $limit,
            $offset,
            $sorts,
            $options
        );
    }
    /**
     * Retrieve the documents matching the specified criteria and return a page with total count.
     *
     * @param string   $parentId
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return mixed
     */
    public function findWithTotal($parentId, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        return [
            $this->find($parentId, $criteria, $fields, $limit, $offset, $sorts, $options),
            $this->count($parentId, $criteria, $options),
        ];
    }
    /**
     * Create a new document by selecting parent from a specific field.
     *
     * @param string $parentFieldName
     * @param mixed  $parentFieldValue
     * @param mixed  $data
     * @param array  $options
     *
     * @return mixed
     */
    public function createBy($parentFieldName, $parentFieldValue, $data, $options = [])
    {
        return $this->create(
            $this->getParentIdBy($parentFieldName, $parentFieldValue),
            $data,
            $options
        );
    }
    /**
     * Create documents if not exist or delete them.
     *
     * @param string $parentId
     * @param mixed  $bulkData
     * @param array  $options
     *
     * @return mixed
     */
    public function createOrDeleteBulk($parentId, $bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $toCreate = [];
        $toDelete = [];

        foreach ($bulkData as $i => $data) {
            if (isset($data['id']) && $this->has($parentId, $data['id'])) {
                $toDelete[$i] = $data;
            } else {
                $toCreate[$i] = $data;
            }
            unset($bulkData[$i]);
        }

        unset($bulkData);

        $docs = [];

        if (count($toCreate)) {
            $docs += $this->createBulk($parentId, $toCreate, $options);
        }

        unset($toCreate);

        if (count($toDelete)) {
            $docs += $this->deleteBulk($parentId, $toDelete, $options);
        }

        unset($toDelete);

        return $docs;
    }
    /**
     * Create document if not exist or update it.
     *
     * @param string $parentId
     * @param mixed  $data
     * @param array  $options
     *
     * @return mixed
     */
    public function createOrUpdate($parentId, $data, $options = [])
    {
        if (isset($data['id']) && $this->has($parentId, $data['id'])) {
            $id = $data['id'];
            unset($data['id']);

            return $this->update($parentId, $id, $data, $options);
        }

        return $this->create($parentId, $data, $options);
    }
    /**
     * Create documents if not exist or update them.
     *
     * @param string $parentId
     * @param mixed  $bulkData
     * @param array  $options
     *
     * @return mixed
     */
    public function createOrUpdateBulk($parentId, $bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $toCreate = [];
        $toUpdate = [];

        foreach ($bulkData as $i => $data) {
            unset($bulkData[$i]);
            if (isset($data['id']) && $this->has($parentId, $data['id'])) {
                $toUpdate[$i] = $data;
            } else {
                $toCreate[$i] = $data;
            }
        }

        unset($bulkData);

        $docs = [];

        if (count($toCreate)) {
            $docs += $this->createBulk($parentId, $toCreate, $options);
        }

        unset($toCreate);

        if (count($toUpdate)) {
            $docs += $this->updateBulk($parentId, $toUpdate, $options);
        }

        unset($toUpdate);

        return $docs;
    }
    /**
     * Delete the specified document.
     *
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function delete($parentId, $id, $options = [])
    {
        list($old) = $this->prepareDelete($parentId, $id, $options);

        $this->saveDelete($parentId, $id, $options);

        return $this->completeDelete($parentId, $id, $old, $options);
    }
    /**
     * Delete the specified documents.
     *
     * @param string $parentId
     * @param array  $ids
     * @param array  $options
     *
     * @return mixed
     */
    public function deleteBulk($parentId, $ids, $options = [])
    {
        $this->checkBulkData($ids, $options);

        $olds     = [];
        $deleteds = [];

        foreach ($ids as $id) {
            list($olds[$id])  = $this->prepareDelete($parentId, $id, $options);
            $this->pushDeleteInBulk($deleteds, $id);
        }

        if (count($deleteds)) {
            $this->saveDeleteBulk($parentId, $deleteds, $options);
        }

        foreach ($ids as $id) {
            $deleteds[$id] = $this->completeDelete($parentId, $id, $olds[$id], $options);
            unset($olds[$id], $ids[$id]);
        }

        unset($ods, $ids);

        return $deleteds;
    }
    /**
     * Returns the parent id based on the specified field and value to select it.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return string
     */
    public function getParentIdBy($field, $value)
    {
        return (string) $this->getRepository()->get([$field => $value], ['_id'])['_id'];
    }
    /**
     * Purge all the documents matching the specified criteria.
     *
     * @param string $parentId
     * @param array  $criteria
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function purge($parentId, $criteria = [], $options = [])
    {
        if ([] !== $criteria) {
            throw $this->createUnexpectedException('Purging sub documents with criteria not supported');
        }

        unset($criteria);

        $this->savePurge($parentId, [], $options);
        $this->event($parentId, 'purged');

        return $this;
    }
    /**
     * Replace all the specified documents.
     *
     * @param string $parentId
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     */
    public function replaceAll($parentId, $data, $options = [])
    {
        $this->saveDeleteFound($parentId, [], $options);
        $this->event($parentId, 'emptied');

        return $this->createBulk($parentId, $data, $options);
    }
    /**
     * Replace all the specified documents.
     *
     * @param mixed $parentId
     * @param array $bulkData
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function replaceBulk($parentId, $bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $ids = [];

        foreach ($bulkData as $k => $v) {
            if (!isset($v['id'])) {
                throw $this->createRequiredException('Missing id for item #%s', $k);
            }
            $ids[] = $v['id'];
        }

        $this->deleteBulk($parentId, $ids, $options);

        unset($ids);

        return $this->createBulk($parentId, $bulkData, $options);
    }
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

        $docs    = [];
        $changes = [];
        $olds    = [];
        $arrays  = [];
        $transitions = [];

        foreach ($bulkData as $i => $data) {
            $id = $data['id'];
            unset($data['id']);
            list($docs[$i],     $arrays[$i], $olds[$i], $transitions[$i]) = $this->prepareUpdate($parentId, $id, $data, $options);
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
     * @param mixed $parentId
     * @param array $array
     * @param array $options
     */
    protected function saveCreate($parentId, array $array, array $options = [])
    {
        $this->getRepository()->setHashProperty($parentId, $this->getRepoKey([$array['id']]), $array, $options);
    }
    /**
     * @param mixed $parentId
     * @param array $arrays
     * @param array $options
     */
    protected function saveCreateBulk($parentId, array $arrays, array $options = [])
    {
        $this->getRepository()->setProperties($parentId, $arrays, $options);
    }
    /**
     * @param array $arrays
     * @param array $array
     *
     * @return mixed|void
     */
    protected function pushCreateInBulk(&$arrays, $array)
    {
        $arrays[$this->mutateKeyToRepoChangesKey('', [$array['id']])] = $array;
    }
    /**
     * @param mixed $parentId
     * @param array $criteria
     * @param array $options
     */
    protected function savePurge($parentId, array $criteria = [], array $options = [])
    {
        unset($criteria);

        $this->getRepository()->resetListProperty($parentId, $this->getRepoKey(), $options);
    }
    /**
     * @param mixed $parentId
     * @param array $criteria
     * @param array $options
     */
    protected function saveDeleteFound($parentId, array $criteria, array $options)
    {
        unset($criteria);

        $this->getRepository()->resetListProperty($parentId, $this->getRepoKey(), $options);
    }
    /**
     * @param mixed $parentId
     * @param mixed $id
     * @param array $options
     */
    protected function saveDelete($parentId, $id, array $options)
    {
        $this->getRepository()->unsetProperty($parentId, $this->getRepoKey([$id]), $options);
    }
    /**
     * @param mixed $parentId
     * @param array $ids
     * @param array $options
     */
    protected function saveDeleteBulk($parentId, $ids, array $options)
    {
        $this->getRepository()->unsetProperty($parentId, array_values($ids), $options);
    }
    /**
     * @param mixed $parentId
     * @param mixed $id
     * @param array $array
     * @param array $options
     */
    protected function saveUpdate($parentId, $id, array $array, array $options)
    {
        $this->getRepository()->setProperties($parentId, $this->mutateArrayToRepoChanges($array, [$id]), $options);
    }
    /**
     * @param mixed $parentId
     * @param array $arrays
     * @param array $options
     */
    protected function saveUpdateBulk($parentId, array $arrays, array $options)
    {
        $this->getRepository()->setProperties($parentId, $arrays, $options);
    }
    /**
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     */
    protected function saveIncrementProperty($parentId, $id, $property, $value, array $options)
    {
        $this->getRepository()->incrementProperty($parentId, $this->getRepoKey([$id, $property]), $value, $options);
    }
    /**
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     */
    protected function saveDecrementProperty($parentId, $id, $property, $value, array $options)
    {
        $this->getRepository()->decrementProperty($parentId, $this->getRepoKey([$id, $property]), $value, $options);
    }
    /**
     * @param array $arrays
     * @param mixed $id
     */
    protected function pushDeleteInBulk(&$arrays, $id)
    {
        $arrays[$id] = $this->getRepoKey([$id]);
    }
    /**
     * @param array $arrays
     * @param array $array
     * @param mixed $id
     */
    protected function pushUpdateInBulk(&$arrays, $array, $id)
    {
        $arrays += $this->mutateArrayToRepoChanges($array, [$id]);
    }
    /**
     * Trigger the specified document event if listener are registered.
     *
     * @param string $parentId
     * @param string $event
     * @param mixed  $data
     *
     * @return $this
     */
    protected function event($parentId, $event, $data = null)
    {
        return $this->dispatch(
            $this->buildEventName($event),
            new DocumentEvent($data, $this->buildTypeVars([$parentId]))
        );
    }
    /**
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return array
     */
    protected function prepareDelete($parentId, $id, $options = [])
    {
        $old = $this->get($parentId, $id, [], $options);

        $this->applyBusinessRules($parentId, 'delete', $old, $options);

        return [$old];
    }
    /**
     * @param string $parentId
     * @param mixed  $id
     * @param mixed  $old
     * @param array  $options
     *
     * @return mixed
     */
    protected function completeDelete($parentId, $id, $old, $options = [])
    {
        $old = $this->cleanModel($old, ['operation' => 'delete', 'parentId' => $parentId] + $options);

        $this->applyBusinessRules($parentId, 'complete_delete', $old, $options);
        $this->event($parentId, 'deleted', $old);

        unset($old);

        return ['id' => $id, 'status' => 'deleted'];
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
}
