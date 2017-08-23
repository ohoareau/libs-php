<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryDbCollection
{
    use Traits\BaseTrait;
    /**
     * @param array    $criteria
     * @param array    $fields
     * @param int|null $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return array
     */
    public function find(array $criteria = [], array $fields = [], $limit = null, $offset = 0, array $sorts = [], array $options = [])
    {
        return [];
    }
    /**
     * @param array $criteria
     * @param array $fields
     * @param array $options
     *
     * @return mixed
     */
    public function findOne(array $criteria = [], array $fields = [], array $options = [])
    {
        return null;
    }
    /**
     * @param array $criteria
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function update(array $criteria, array $data, array $options = [])
    {
        return null;
    }
    /**
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function insert(array $data, array $options = [])
    {
        return null;
    }
    /**
     * @param array $criteria
     * @param array $options
     *
     * @return mixed
     */
    public function remove(array $criteria, array $options = [])
    {
        return null;
    }
    /**
     * @param array $bulkData
     * @param array $options
     *
     * @return mixed
     */
    public function batchInsert(array $bulkData, array $options = [])
    {
        return null;
    }
    /**
     * @param array $fields
     * @param array $index
     *
     * @return mixed
     */
    public function createIndex(array $fields, array $index)
    {
        return null;
    }
    /**
     * @param array $index
     *
     * @return mixed
     */
    public function deleteIndex(array $index)
    {
        return null;
    }
}
