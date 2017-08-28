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
 * Create or Update service trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CreateOrUpdateServiceTrait
{
    /**
     * @param string $parentId
     * @param mixed  $id
     * @param array  $options
     *
     * @return bool
     */
    abstract public function has($parentId, $id, $options = []);
    /**
     * @param string $parentId
     * @param string $id
     * @param array  $data
     * @param array  $options
     *
     * @return $this
     */
    abstract public function update($parentId, $id, $data, $options = []);
    /**
     * @param string $parentId
     * @param mixed  $data
     * @param array  $options
     *
     * @return mixed
     */
    abstract public function create($parentId, $data, $options = []);
    /**
     * @param string $parentId
     * @param mixed  $bulkData
     * @param array  $options
     *
     * @return mixed
     */
    abstract public function createBulk($parentId, $bulkData, $options = []);
    /**
     * @param string $parentId
     * @param array  $bulkData
     * @param array  $options
     *
     * @return mixed
     */
    abstract public function updateBulk($parentId, $bulkData, $options = []);
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
     * @param mixed $bulkData
     * @param array $options
     *
     * @return $this
     *
     * @throws Exception
     */
    abstract protected function checkBulkData($bulkData, $options = []);
}
