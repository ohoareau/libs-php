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

/**
 * Delete service trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DeleteServiceTrait
{
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
            list($olds[$id]) = $this->prepareDelete($parentId, $id, $options);
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
}
