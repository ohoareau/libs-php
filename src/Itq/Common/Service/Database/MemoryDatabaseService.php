<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Database;

use Closure;
use Itq\Common\MemoryDb;
use Itq\Common\MemoryDbCollection;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryDatabaseService extends Base\AbstractOperationAwareDatabaseService
{
    /**
     * @return $this
     */
    public function drop()
    {
        /** @var MemoryDb $db */
        $connection = $this->getConnection();
        $db         = $connection->getBackend();
        $db->dropDatabase($connection->getParameter('db'));

        return $this;
    }
    /**
     * @param string $name
     * @param array  $options
     *
     * @return mixed
     */
    public function getCollection($name, $options = [])
    {
        $connection = $this->getConnection(['collection' => $name] + $options);

        return $connection->getBackend()->selectCollection($connection->getParameter('db'), $name);
    }
    /**
     * @return Closure[]
     */
    protected function getAvailableOperations()
    {
        return [
            'find' => function (MemoryDbCollection $collection, $criteria, $fields, $limit, $offset, $sorts) {
                return $collection->find($criteria, $fields, $limit, $offset, $sorts);
            },
            'findOne' => function (MemoryDbCollection $collection, $criteria, $fields) {
                return $collection->findOne($criteria, $fields);
            },
            'update' => function (MemoryDbCollection $collection, $criteria, $data, $options) {
                return $collection->update($criteria, $data, $options);
            },
            'insert' => function (MemoryDbCollection $collection, $data, $options) {
                return $collection->insert($data, $options);
            },
            'remove' => function (MemoryDbCollection $collection, $criteria, $options) {
                return $collection->remove($criteria, $options);
            },
            'batchInsert' => function (MemoryDbCollection $collection, $bulkData, $options) {
                return $collection->batchInsert($bulkData, $options);
            },
            'createIndex' => function (MemoryDbCollection $collection, $fields, $index) {
                return $collection->createIndex($fields, $index);
            },
            'deleteIndex' => function (MemoryDbCollection $collection, $index) {
                return $collection->deleteIndex($index);
            },
        ];
    }
}
