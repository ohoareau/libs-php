<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

/**
 * Database Service Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface DatabaseServiceInterface
{
    /**
     * Insert a single record into the specified partition.
     *
     * @param string $partition
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function insert($partition, $data = [], $options = []);
    /**
     * Update the first record matching criteria in the specified partition.
     *
     * @param string $partition
     * @param array  $criteria
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function update($partition, $criteria = [], $data = [], $options = []);
    /**
     * Remove the first record matching criteria from the specified partition.
     *
     * @param string $partition
     * @param array  $criteria
     * @param array  $options
     *
     * @return mixed
     */
    public function remove($partition, $criteria = [], $options = []);
    /**
     * Retrieve a list of records matching criteria from the specified partition.
     *
     * @param string   $partition
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return array|\Iterator
     *
     * @throws \Exception
     */
    public function find($partition, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = []);
    /**
     * Retrieve one record matching criteria, if exist, from the specified partition.
     *
     * @param string $partition
     * @param array  $criteria
     * @param array  $fields
     * @param array  $options
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function findOne($partition, $criteria = [], $fields = [], $options = []);
    /**
     * Count the records matching the criteria in the specified partition.
     *
     * @param string $partition
     * @param array  $criteria
     * @param array  $options
     *
     * @return int
     */
    public function count($partition, $criteria = [], $options = []);
    /**
     * Insert a list of records into the specified partition.
     *
     * @param string $partition
     * @param array  $bulkData
     * @param array  $options
     *
     * @return mixed
     */
    public function bulkInsert($partition, $bulkData = [], $options = []);
    /**
     * Drop the current database.
     *
     * @return $this
     */
    public function drop();
    /**
     * Ensures the specified index is present on the specified fields of the partition.
     *
     * @param string       $partition
     * @param string|array $fields
     * @param mixed        $index
     * @param array        $options
     *
     * @return bool
     */
    public function ensureIndex($partition, $fields, $index, $options = []);
    /**
     * Drop the the specified index.
     *
     * @param string $partition
     * @param string $index
     * @param array  $options
     *
     * @return array
     */
    public function dropIndex($partition, $index, $options = []);
}
