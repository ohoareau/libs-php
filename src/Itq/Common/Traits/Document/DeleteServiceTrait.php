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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DeleteServiceTrait
{
    /**
     * @param mixed $id
     * @param array $fields
     * @param array $options
     *
     * @return mixed
     */
    abstract public function get($id, $fields = [], $options = []);
    /**
     * Delete the specified document.
     *
     * @param mixed $id
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function delete($id, $options = [])
    {
        list($old) = $this->prepareDelete($id, $options);

        $this->saveDelete($id, $options);

        return $this->completeDelete($id, $old, $options);
    }
    /**
     * Delete the specified documents.
     *
     * @param array $ids
     * @param array $options
     *
     * @return mixed
     */
    public function deleteBulk($ids, $options = [])
    {
        $this->checkBulkData($ids, $options);

        $olds     = [];
        $deleteds = [];

        foreach ($ids as $id) {
            list($olds[$id]) = $this->prepareDelete($id, $options);
        }


        foreach ($this->saveDeleteBulk($ids, $options) as $id) {
            $deleteds[$id] = $this->completeDelete($id, $olds[$id], $options);
            unset($olds[$id]);
        }

        unset($ids);
        unset($olds);

        return $deleteds;
    }
    /**
     * @param mixed $id
     * @param array $options
     *
     * @return array
     */
    protected function prepareDelete($id, $options = [])
    {
        $old = $this->get($id, [], $options);

        $this->restrictModel($old, ['operation' => 'delete'] + $options);
        $this->applyBusinessRules('delete', $old, $options);

        return [$old];
    }
    /**
     * @param mixed $id
     * @param mixed $old
     * @param array $options
     *
     * @return mixed
     */
    protected function completeDelete($id, $old, $options = [])
    {
        $this->cleanModel($old, ['operation' => 'delete'] + $options);
        $this->applyBusinessRules('complete_delete', $old, $options);
        $this->event('deleted', $old);

        unset($old);

        return ['id' => $id, 'status' => 'deleted'];
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return mixed|void
     */
    abstract protected function saveDelete($id, array $options);
    /**
     * @param array $ids
     * @param array $options
     *
     * @return array
     */
    abstract protected function saveDeleteBulk($ids, array $options);
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
     * @param mixed $doc
     * @param array $options
     *
     * @throws Exception
     */
    abstract protected function restrictModel($doc, array $options = []);
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
