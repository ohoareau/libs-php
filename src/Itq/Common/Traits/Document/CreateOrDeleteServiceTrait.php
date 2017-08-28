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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CreateOrDeleteServiceTrait
{
    /**
     * @param array $ids
     * @param array $options
     *
     * @return mixed
     */
    abstract public function deleteBulk($ids, $options = []);
    /**
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    abstract public function createBulk($bulkData, $options = []);
    /**
     * @param mixed $id
     * @param array $options
     *
     * @return bool
     */
    abstract public function has($id, $options = []);
    /**
     * Create documents if not exist or delete them.
     *
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function createOrDeleteBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $toCreate = [];
        $toDelete = [];

        foreach ($bulkData as $i => $data) {
            if (isset($data['id']) && $this->has($data['id'])) {
                $toDelete[$i] = $data;
            } else {
                $toCreate[$i] = $data;
            }
            unset($bulkData[$i]);
        }

        unset($bulkData);

        $docs = [];

        if (count($toCreate)) {
            $docs += $this->createBulk($toCreate, $options);
        }

        unset($toCreate);

        if (count($toDelete)) {
            $docs += $this->deleteBulk($toDelete, $options);
        }

        unset($toDelete);

        return $docs;
    }
    /**
     * @param mixed $bulkData
     * @param array $options
     *
     * @return $this
     */
    abstract protected function checkBulkData($bulkData, $options = []);
}
