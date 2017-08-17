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
trait CreateOrUpdateServiceTrait
{
    /**
     * Create document if not exist or update it.
     *
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function createOrUpdate($data, $options = [])
    {
        if (isset($data['id']) && $this->has($data['id'])) {
            $id = $data['id'];
            unset($data['id']);

            return $this->update($id, $data, $options);
        }

        return $this->create($data, $options);
    }
    /**
     * Create documents if not exist or update them.
     *
     * @param mixed $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function createOrUpdateBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $toCreate = [];
        $toUpdate = [];

        foreach ($bulkData as $i => $data) {
            unset($bulkData[$i]);
            if (isset($data['id']) && $this->has($data['id'])) {
                $toUpdate[$i] = $data;
            } else {
                $toCreate[$i] = $data;
            }
        }

        unset($bulkData);

        $docs = [];

        if (count($toCreate)) {
            $docs += $this->createBulk($toCreate, $options);
        }

        unset($toCreate);

        if (count($toUpdate)) {
            $docs += $this->updateBulk($toUpdate, $options);
        }

        unset($toUpdate);

        return $docs;
    }
}
