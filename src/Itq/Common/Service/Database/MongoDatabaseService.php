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

use MongoId;
use Exception;
use MongoCursor;
use MongoClient;
use MongoCollection;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MongoDatabaseService extends Base\AbstractOperationAwareDatabaseService
{
    /**
     * @return $this
     */
    public function drop()
    {
        /** @var MongoClient $client */
        $connection = $this->getConnection();
        $client     = $connection->getBackend();
        $client->selectDB($connection->getParameter('db'))->drop();

        return $this;
    }
    /**
     * @param string $name
     * @param array  $options
     *
     * @return MongoCollection
     */
    public function getCollection($name, $options = [])
    {
        $connection = $this->getConnection(['collection' => $name] + $options);

        return $connection->getBackend()->selectCollection($connection->getParameter('db'), $name);
    }
    /**
     * @return array
     */
    protected function getAvailableOperations()
    {
        return [
            'find' => function (MongoCollection $collection, $criteria, $fields, $limit, $offset, $sorts) {
                $result = $collection->find($criteria);
                if (count($fields)) {
                    $result->fields($fields);
                }
                if (count($sorts)) {
                    $result->sort($sorts);
                }
                if (is_numeric($offset) && $offset > 0) {
                    $result->skip($offset);
                }
                if (is_numeric($limit) && $limit > 0) {
                    $result->limit($limit);
                }

                return $result;
            },
            'findOne' => function (MongoCollection $collection, $criteria, $fields) {
                return $collection->findOne($criteria, $fields);
            },
            'update' => function (MongoCollection $collection, $criteria, $data, $options) {
                return $collection->update($criteria, $data, $options);
            },
            'insert' => function (MongoCollection $collection, $data, $options) {
                return $collection->insert($data, $options);
            },
            'remove' => function (MongoCollection $collection, $criteria, $options) {
                return $collection->remove($criteria, $options);
            },
            'batchInsert' => function (MongoCollection $collection, $bulkData, $options) {
                return $collection->batchInsert($bulkData, $options);
            },
            'createIndex' => function (MongoCollection $collection, $fields, $index) {
                return $collection->createIndex($fields, $index);
            },
            'deleteIndex' => function (MongoCollection $collection, $index) {
                return $collection->deleteIndex($index);
            },
        ];
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isValidId($value)
    {
        return is_object($value) && $value instanceof MongoId;
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function castId($value)
    {
        if (!preg_match('/^[a-f0-9]{24}$/', $value)) {
            throw $this->createMalformedException('db.id.malformed_mongo');
        }

        return new MongoId($value);
    }
    /**
     * @param MongoCursor $result
     *
     * @return int
     */
    protected function countResult($result)
    {
        return $result->count(true);
    }
}
