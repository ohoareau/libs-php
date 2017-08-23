<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Database\Base;

use Closure;
use Iterator;
use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractOperationAwareDatabaseService extends AbstractDatabaseService
{
    /**
     * @param string $name
     * @param array  $options
     *
     * @return mixed
     */
    abstract public function getCollection($name, $options = []);
    /**
     * @param string $name
     *
     * @return Closure
     */
    public function getOperation($name)
    {
        return $this->getArrayParameterKey('operations', $name);
    }
    /**
     * @param string $collection
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function insert($collection, $data = [], $options = [])
    {
        $this->start();

        try {
            $data   = $this->buildData($data);
            $result = $this->writeOn($collection, 'insert', $options, [$data, $options]);
            $this->logQuery($collection, 'insert', null, $data, $result, ['options' => $options]);

            return $result;
        } catch (Exception $e) {
            $this->logQuery($collection, 'insert', null, $data, null, ['options' => $options], $e);
            throw $e;
        }
    }
    /**
     * @param string $collection
     * @param array  $criteria
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function update($collection, $criteria = [], $data = [], $options = [])
    {
        $this->start();

        try {
            $criteria = $this->buildCriteria($criteria);
            $data     = $this->buildData($data);
            $result   = $this->writeOn($collection, 'update', $options, [$criteria, $data, $options]);
            $this->logQuery($collection, 'update', $criteria, $data, $result, ['options' => $options]);

            return $result;
        } catch (Exception $e) {
            $this->logQuery($collection, 'update', $criteria, $data, null, ['options' => $options], $e);
            throw $e;
        }
    }
    /**
     * @param string $collection
     * @param array  $criteria
     * @param array  $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function remove($collection, $criteria = [], $options = [])
    {
        $this->start();

        try {
            $criteria = $this->buildCriteria($criteria);
            $result   = $this->writeOn($collection, 'remove', $options, [$criteria, $options]);
            $this->logQuery($collection, 'remove', $criteria, null, $result, ['options' => $options]);

            return $result;
        } catch (Exception $e) {
            $this->logQuery($collection, 'remove', $criteria, null, null, ['options' => $options], $e);
            throw $e;
        }
    }
    /**
     * @param string   $collection
     * @param array    $criteria
     * @param array    $fields
     * @param null|int $limit
     * @param int      $offset
     * @param array    $sorts
     * @param array    $options
     *
     * @return array|Iterator
     *
     * @throws Exception
     */
    public function find($collection, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        $this->start();

        try {
            list($cacheKey, $cachedData) = $this->getCache(['find', $collection, $criteria, $fields, $limit, $offset, $sorts], $options);
            if (null !== $cachedData) {
                $this->logQuery($collection, ['cachedFind', 'find'], $criteria, null, $cachedData, ['fields' => $fields, 'limit' => $limit, 'sort' => $sorts, 'skip' => $offset, 'options' => $options]);

                return $cachedData;
            }

            $criteria = $this->buildCriteria($criteria);
            $fields   = $this->buildFields($fields);
            $sorts    = $this->buildSorts($sorts);
            $result   = $this->readFrom($collection, 'find', $options, [$criteria, $fields, $limit, $offset, $sorts, $options]);

            $this->logQuery($collection, 'find', $criteria, null, $result, ['fields' => $fields, 'limit' => $limit, 'sort' => $sorts, 'skip' => $offset, 'options' => $options]);
            $this->setCachedData($cacheKey, $result);

            return $result;
        } catch (Exception $e) {
            $this->logQuery($collection, 'find', $criteria, null, null, ['fields' => $fields, 'limit' => $limit, 'sort' => $sorts, 'skip' => $offset, 'options' => $options], $e);
            throw $e;
        }
    }
    /**
     * @param string $collection
     * @param array  $criteria
     * @param array  $fields
     * @param array  $options
     *
     * @return array|null
     *
     * @throws Exception
     */
    public function findOne($collection, $criteria = [], $fields = [], $options = [])
    {
        $this->start();

        try {
            list($cacheKey, $cachedData) = $this->getCache(['findOne', $collection, $criteria, $fields], $options);
            if (null !== $cachedData) {
                $this->logQuery($collection, ['cachedFindOne', 'findOne'], $criteria, null, $cachedData, ['fields' => $fields, 'options' => $options]);

                return $cachedData;
            }
            $criteria = $this->buildCriteria($criteria);
            $fields   = $this->buildFields($fields);
            $result   = $this->readFrom($collection, 'findOne', $options, [$criteria, $fields]);
            $this->logQuery($collection, 'findOne', $criteria, null, $result, ['fields' => $fields, 'options' => $options]);
            $this->setCachedData($cacheKey, $result);

            return $result;
        } catch (Exception $e) {
            $this->logQuery($collection, 'findOne', $criteria, null, null, ['fields' => $fields, 'options' => $options], $e);
            throw $e;
        }
    }
    /**
     * @param string $collection
     * @param array  $criteria
     * @param array  $options
     *
     * @return int
     */
    public function count($collection, $criteria = [], $options = [])
    {
        return $this->countResult($this->find($collection, $criteria, ['_id'], null, 0, [], $options));
    }
    /**
     * @param string $collection
     * @param array  $bulkData
     * @param array  $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function bulkInsert($collection, $bulkData = [], $options = [])
    {
        $this->start();

        try {
            $bulkData = $this->buildBulkData($bulkData);
            $result   = $this->writeOn($collection, 'batchInsert', $options, [$bulkData, $options]);
            $this->logQuery($collection, 'bulkInsert', null, $bulkData, $result, ['options' => $options]);

            return $result;
        } catch (Exception $e) {
            $this->logQuery($collection, 'bulkInsert', null, $bulkData, null, ['options' => $options], $e);
            throw $e;
        }
    }
    /**
     * @param string       $collection
     * @param string|array $fields
     * @param mixed        $index
     * @param array        $options
     *
     * @return array
     */
    public function ensureIndex($collection, $fields, $index, $options = [])
    {
        return $this->writeOn($collection, 'createIndex', $options, [is_array($fields) ? $fields : [$fields => true], $index]);
    }
    /**
     * @param string       $collection
     * @param array|string $index
     * @param array        $options
     *
     * @return array
     */
    public function dropIndex($collection, $index, $options = [])
    {
        return $this->writeOn($collection, 'deleteIndex', $options, [$index]);
    }
    /**
     * @param mixed $result
     *
     * @return int
     */
    protected function countResult($result)
    {
        return is_array($result) ? count($result) : 0;
    }
    /**
     * @param string $collection
     * @param string $operation
     * @param array  $options
     * @param array  $args
     *
     * @return mixed
     */
    protected function writeOn($collection, $operation, array $options, array $args)
    {
        return $this->operateOn($collection, 'write', $operation, $options, $args);
    }
    /**
     * @param string $collection
     * @param string $operation
     * @param array  $options
     * @param array  $args
     *
     * @return mixed
     */
    protected function readFrom($collection, $operation, array $options, array $args)
    {
        return $this->operateOn($collection, 'read', $operation, $options, $args);
    }
    /**
     * @param string $collection
     * @param string $type
     * @param string $operation
     * @param array  $options
     * @param array  $args
     *
     * @return mixed
     */
    protected function operateOn($collection, $type, $operation, array $options, array $args)
    {
        return $this->operate(
            $this->getCollection($collection, ['operation' => $operation, 'operationType' => $type] + $options),
            $operation,
            $args,
            $options
        );
    }
    /**
     * @return Closure[]
     */
    abstract protected function getAvailableOperations();
    /**
     * @param mixed  $model
     * @param string $operation
     * @param array  $args
     * @param array  $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function operate($model, $operation, array $args, array $options)
    {
        unset($options);

        return call_user_func_array($this->getOperation($operation), array_merge([$model], $args));
    }
    /**
     *
     */
    protected function init()
    {
        $this->setParameter('operations', $this->getAvailableOperations());
    }
}
