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

use Itq\Common\Event;
use Itq\Common\Traits;
use Itq\Common\Service;

use MongoId;
use MongoCollection;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MongoDatabaseService extends Base\AbstractDatabaseService
{
    use Traits\ServiceAware\StorageServiceAwareTrait;
    use Traits\ServiceAware\GeneratorServiceAwareTrait;
    /**
     * @param Service\ConnectionService $connectionService
     * @param EventDispatcherInterface  $eventDispatcher
     * @param Service\StorageService    $storageService
     * @param Service\GeneratorService  $generatorService
     *
     * @throws \Exception
     */
    public function __construct(
        Service\ConnectionService $connectionService,
        EventDispatcherInterface $eventDispatcher,
        Service\StorageService $storageService,
        Service\GeneratorService $generatorService
    ) {
        parent::__construct($connectionService, $eventDispatcher);

        $this->setStorageService($storageService);
        $this->setGeneratorService($generatorService);
    }
    /**
     * @return string
     */
    public function getDatabaseType()
    {
        return 'mongo';
    }
    /**
     * Insert a single record into the specified partition.
     *
     * @param string $collection
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function insert($collection, $data = [], $options = [])
    {
        $this->start();

        try {
            $builtData = $this->buildData($data);
            $result    = $this
                ->getCollection($collection, ['operation' => 'insert', 'operationType' => 'write'] + $options)
                ->insert($builtData, $options)
            ;

            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('insert', 'db.'.$collection.'.insert('.json_encode($builtData).')', ['data' => $builtData, 'options' => $options], $startDate, $endDate, $result));

            return $result;
        } catch (\Exception $e) {
            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('insert', 'db.'.$collection.'.insert('.json_encode($data).')', ['rawData' => $data, 'options' => $options], $startDate, $endDate, null, $e));
            throw $e;
        }
    }
    /**
     * Update the first record matching criteria in the specified partition.
     *
     * @param string $collection
     * @param array  $criteria
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function update($collection, $criteria = [], $data = [], $options = [])
    {
        $this->start();

        try {
            $builtCriteria = $this->buildCriteria($criteria);
            $builtData     = $this->buildData($data);
            $result        = $this
                ->getCollection($collection, ['operation' => 'update', 'operationType' => 'write'] + $options)
                ->update($builtCriteria, $builtData, $options)
            ;

            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('update', 'db.'.$collection.'.update('.json_encode($builtCriteria).', '.json_encode($builtData).')', ['collection' => $collection, 'criteria' => $builtCriteria, 'data' => $builtData, 'options' => $options], $startDate, $endDate, $result));

            return $result;
        } catch (\Exception $e) {
            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('update', 'db.'.$collection.'.update('.json_encode($criteria).', '.json_encode($data).')', ['rawCriteria' => $criteria, 'rawData' => $data, 'options' => $options], $startDate, $endDate, null, $e));
            throw $e;
        }
    }
    /**
     * Remove the first record matching criteria from the specified partition.
     *
     * @param string $collection
     * @param array  $criteria
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function remove($collection, $criteria = [], $options = [])
    {
        $this->start();

        try {
            $builtCriteria = $this->buildCriteria($criteria);
            $result        = $this
                ->getCollection($collection, ['operation' => 'remove', 'operationType' => 'write'] + $options)
                ->remove($builtCriteria, $options)
            ;

            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('remove', 'db.'.$collection.'.remove('.json_encode($builtCriteria).')', ['collection' => $collection, 'criteria' => $builtCriteria, 'options' => $options], $startDate, $endDate, $result));

            return $result;
        } catch (\Exception $e) {
            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('remove', 'db.'.$collection.'.remove('.json_encode($criteria).')', ['rawCriteria' => $criteria, 'options' => $options], $startDate, $endDate, null, $e));
            throw $e;
        }
    }
    /**
     * Retrieve a list of records matching criteria from the specified partition.
     *
     * @param string   $collection
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
    public function find($collection, $criteria = [], $fields = [], $limit = null, $offset = 0, $sorts = [], $options = [])
    {
        $this->start();

        try {
            list($cacheKey, $cachedData) = $this->getCache(['find', $collection, $criteria, $fields, $limit, $offset, $sorts], $options);
            if (null !== $cachedData) {
                list ($startDate, $endDate) = $this->stop();
                $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('cachedFind', 'db.'.$collection.'.findOne('.json_encode($criteria).', '.json_encode($fields).')', ['rawCriteria' => $criteria, 'rawFields' => $fields, 'limit' => $limit, 'sort' => $sorts, 'skip' => $offset, 'options' => $options], $startDate, $endDate, $cachedData));

                return $cachedData;
            }

            $builtCriteria = $this->buildCriteria($criteria);
            $builtFields   = $this->buildFields($fields);
            $builtSorts    = $this->buildSorts($sorts);
            $cursor        = $this
                ->getCollection($collection, ['operation' => 'find', 'operationType' => 'read'] + $options)
                ->find($builtCriteria)
            ;

            if (count($builtFields)) {
                $cursor->fields($builtFields);
            }
            if (count($builtSorts)) {
                $cursor->sort($builtSorts);
            }
            if (is_numeric($offset) && $offset > 0) {
                $cursor->skip($offset);
            }
            if (is_numeric($limit) && $limit > 0) {
                $cursor->limit($limit);
            }

            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('find', 'db.'.$collection.'.find('.json_encode($builtCriteria).', '.json_encode($builtFields).')', ['criteria' => $builtCriteria, 'fields' => $builtFields, 'limit' => $limit, 'sort' => $builtSorts, 'skip' => $offset, 'options' => $options], $startDate, $endDate, $cursor));

            $this->setCachedData($cacheKey, $cursor);

            return $cursor;
        } catch (\Exception $e) {
            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('find', 'db.'.$collection.'.find('.json_encode($criteria).', '.json_encode($fields).')', ['rawCriteria' => $criteria, 'rawFields' => $fields, 'limit' => $limit, 'rawSort' => $sorts, 'skip' => $offset, 'options' => $options], $startDate, $endDate, null, $e));
            throw $e;
        }
    }
    /**
     * Retrieve one record matching criteria, if exist, from the specified partition.
     *
     * @param string $collection
     * @param array  $criteria
     * @param array  $fields
     * @param array  $options
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function findOne($collection, $criteria = [], $fields = [], $options = [])
    {
        $this->start();

        try {
            list($cacheKey, $cachedData) = $this->getCache(['findOne', $collection, $criteria, $fields], $options);
            if (null !== $cachedData) {
                list ($startDate, $endDate) = $this->stop();
                $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('cachedFindOne', 'db.'.$collection.'.findOne('.json_encode($criteria).', '.json_encode($fields).')', ['rawCriteria' => $criteria, 'rawFields' => $fields, 'options' => $options], $startDate, $endDate, $cachedData));

                return $cachedData;
            }
            $builtCriteria = $this->buildCriteria($criteria);
            $builtFields   = $this->buildFields($fields);
            $result        = $this
                ->getCollection($collection, ['operation' => 'findOne', 'operationType' => 'read'] + $options)
                ->findOne($builtCriteria, $builtFields)
            ;

            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('findOne', 'db.'.$collection.'.findOne('.json_encode($builtCriteria).', '.json_encode($builtFields).')', ['criteria' => $builtCriteria, 'fields' => $builtFields, 'options' => $options], $startDate, $endDate, $result));
            $this->setCachedData($cacheKey, $result);

            return $result;
        } catch (\Exception $e) {
            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('findOne', 'db.'.$collection.'.findOne('.json_encode($criteria).', '.json_encode($fields).')', ['rawCriteria' => $criteria, 'rawFields' => $fields, 'options' => $options], $startDate, $endDate, null, $e));
            throw $e;
        }
    }
    /**
     * Count the records matching the criteria in the specified partition.
     *
     * @param string $collection
     * @param array  $criteria
     * @param array  $options
     *
     * @return int
     */
    public function count($collection, $criteria = [], $options = [])
    {
        /** @var \MongoCursor $cursor */
        $cursor = $this->find($collection, $criteria, ['_id'], null, 0, [], $options);

        return $cursor->count(true);
    }
    /**
     * Insert a list of records into the specified partition.
     *
     * @param string $collection
     * @param array  $bulkData
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function bulkInsert($collection, $bulkData = [], $options = [])
    {
        $this->start();

        try {
            $builtData = $this->buildBulkData($bulkData);
            $result    = $this
                ->getCollection($collection, ['operation' => 'batchInsert', 'operationType' => 'write'] + $options)
                ->batchInsert($builtData, $options)
            ;

            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('bulkInsert', 'db.'.$collection.'.bulkInsert('.json_encode($builtData).')', ['data' => $builtData, 'options' => $options], $startDate, $endDate, $result));

            return $result;
        } catch (\Exception $e) {
            list ($startDate, $endDate) = $this->stop();
            $this->dispatch('database.query.executed', new Event\DatabaseQueryEvent('bulkInsert', 'db.'.$collection.'.bulkIinsert('.json_encode($bulkData).')', ['rawData' => $bulkData, 'options' => $options], $startDate, $endDate, null, $e));
            throw $e;
        }
    }
    /**
     * Drop the current database.
     *
     * @return $this
     */
    public function drop()
    {
        $connection = $this->getConnection();
        $client     = $connection->getBackend();

        /** @var \MongoClient $client */

        $client->selectDB($connection->getParameter('db'))->drop();

        return $this;
    }
    /**
     * Ensures the specified index is present on the specified fields of the partition.
     *
     * @param string       $collection
     * @param string|array $fields
     * @param mixed        $index
     * @param array        $options
     *
     * @return array
     */
    public function ensureIndex($collection, $fields, $index, $options = [])
    {
        return $this
            ->getCollection($collection, ['operation' => 'ensureIndex', 'operationType' => 'write'] + $options)
            ->createIndex(is_array($fields) ? $fields : [$fields => true], $index)
        ;
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
        return $this
            ->getCollection($collection, ['operation' => 'ensureIndex', 'operationType' => 'write'] + $options)
            ->deleteIndex($index);
    }
    /**
     * Returns the specified Mongo Collection.
     *
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
     * Ensure fields are well formed (array).
     *
     * @param array|mixed $fields
     *
     * @return array
     */
    public function buildFields($fields)
    {
        $cleanedFields = [];

        if (!is_array($fields)) {
            return $cleanedFields;
        }

        $rootFieldsNames = [];

        foreach ($fields as $k => $v) {
            if (is_numeric($k)) {
                if (!is_string($v)) {
                    $v = (string) $v;
                }
                $cleanedFields[$v] = true;
                $k = $v;
            } else {
                if (!is_bool($v)) {
                    $v = (bool) $v;
                }
                $cleanedFields[$k] = $v;
            }
            if (false === strpos($k, '.')) {
                $rootFieldsNames[$k] = true;
            }
        }

        foreach ($cleanedFields as $k => $v) {
            $p = strpos($k, '.');
            if (false === $p) {
                continue;
            }
            $rootFieldName = substr($k, 0, $p);
            if (isset($rootFieldsNames[$rootFieldName])) {
                unset($cleanedFields[$k]);
            }
        }

        return $cleanedFields;
    }
    /**
     * @param array $keyData
     * @param array $options
     *
     * @return array
     */
    protected function getCache(array $keyData, array $options)
    {
        if (!isset($options['cached']) || true !== $options['cached']) {
            return [null, null];
        }

        $cacheKey = sha1(serialize($keyData));
        $value    = $this->getStorageService()->read('/caches/db/mongo/'.$cacheKey, ['defaultValue' => null]);

        if ($value instanceof \MongoCursor) {
            $value->rewind();
        }

        return [$cacheKey, $value];
    }
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    protected function setCachedData($key, $value)
    {
        if (null === $key) {
            return $this;
        }

        $this->getStorageService()->save('/caches/db/mongo/'.$key, $value);

        return $this;
    }
    /**
     * Ensure specified id is a MongoId (convert to MongoId if is string).
     *
     * @param string $id
     *
     * @return \MongoId|array
     *
     * @throws \Exception if malformed
     */
    protected function ensureMongoId($id)
    {
        if (is_object($id) && $id instanceof MongoId) {
            return $id;
        }
        if (is_array($id)) {
            foreach ($id as $k => $iid) {
                $id[$k] = $this->ensureMongoId($iid);
            }

            return $id;
        }
        if (!preg_match('/^[a-f0-9]{24}$/', $id)) {
            throw $this->createMalformedException('db.id.malformed_mongo');
        }

        return new MongoId($id);
    }
    /**
     * Ensure criteria are well formed (array).
     *
     * @param array $criteria
     *
     * @return array
     */
    protected function buildCriteria($criteria)
    {
        if (!is_array($criteria)) {
            return [];
        }
        if (isset($criteria['$or']) && is_array($criteria['$or'])) {
            foreach ($criteria['$or'] as $a => $b) {
                if (isset($b['_id'])) {
                    $criteria['$or'][$a]['_id'] = $this->ensureMongoId($b['_id']);
                }
            }
        }

        $textCriteria = null;

        foreach ($criteria as $k => $_v) {
            if (is_string($_v)) {
                $c = [];
                foreach (explode('*|*', $_v) as $v) {
                    if ('*' === substr($v, 0, 1)) {
                        if ('*notempty*' === $v) {
                            $c += ['$exists' => true];
                        } elseif ('*empty*' === $v) {
                            $c += ['$exists' => false];
                        } elseif ('*true*' === $v) {
                            $c += ['$eq' => true];
                        } elseif ('*false*' === $v) {
                            $c += ['$eq' => false];
                        } elseif ('*not*:' === substr($v, 0, 6)) {
                            $c += ['$ne' => $this->prepareValueForField($k, substr($v, 6))];
                        } elseif ('*ne*:' === substr($v, 0, 5)) {
                            $c += ['$ne' => $this->prepareValueForField($k, substr($v, 5))];
                        } elseif ('*not_int*:' === substr($v, 0, 10)) {
                            $c += ['$ne' => (int) substr($v, 10)];
                        } elseif ('*not_bool*:' === substr($v, 0, 11)) {
                            $c += ['$ne' => (bool) substr($v, 11)];
                        } elseif ('*not_dec*:' === substr($v, 0, 10)) {
                            $c += ['$ne' => (double) substr($v, 10)];
                        } elseif ('*in*:' === substr($v, 0, 5)) {
                            $c += ['$in' => $this->prepareArrayValuesForField($k, explode(',', substr($v, 5)))];
                        } elseif ('*in_int*:' === substr($v, 0, 9)) {
                            $c += ['$in' => array_map(function ($vv) {
                                return (int) $vv;
                            }, explode(',', substr($v, 9))), ];
                        } elseif ('*in_dec*:' === substr($v, 0, 9)) {
                            $c += ['$in' => array_map(function ($vv) {
                                return (double) $vv;
                            }, explode(',', substr($v, 9))), ];
                        } elseif ('*nin*:' === substr($v, 0, 6)) {
                            $c += ['$nin' => $this->prepareArrayValuesForField($k, explode(',', substr($v, 6)))];
                        } elseif ('*nin_int*:' === substr($v, 0, 10)) {
                            $c += ['$nin' => array_map(function ($vv) {
                                return (int) $vv;
                            }, explode(',', substr($v, 10))), ];
                        } elseif ('*nin_dec*:' === substr($v, 0, 10)) {
                            $c += ['$nin' => array_map(function ($vv) {
                                return (double) $vv;
                            }, explode(',', substr($v, 10))), ];
                        } elseif ('*lte_date*:' === substr($v, 0, 11)) {
                            $c += ['$lte' => new \MongoDate((new \DateTime(substr($v, 11)))->getTimestamp())];
                        } elseif ('*lt_date*:' === substr($v, 0, 10)) {
                            $c += ['$lt' => new \MongoDate((new \DateTime(substr($v, 10)))->getTimestamp())];
                        } elseif ('*gte_date*:' === substr($v, 0, 11)) {
                            $c += ['$gte' => new \MongoDate((new \DateTime(substr($v, 11)))->getTimestamp())];
                        } elseif ('*gt_date*:' === substr($v, 0, 10)) {
                            $c += ['$gt' => new \MongoDate((new \DateTime(substr($v, 10)))->getTimestamp())];
                        } elseif ('*lte*:' === substr($v, 0, 6)) {
                            $c += ['$lte' => (double) substr($v, 6)];
                        } elseif ('*lt*:' === substr($v, 0, 5)) {
                            $c += ['$lt' => (double) substr($v, 5)];
                        } elseif ('*gte*:' === substr($v, 0, 6)) {
                            $c += ['$gte' => (double) substr($v, 6)];
                        } elseif ('*gt*:' === substr($v, 0, 5)) {
                            $c += ['$gt' => (double) substr($v, 5)];
                        } elseif ('*eq*:' === substr($v, 0, 5)) {
                            $c += ['$eq' => (double) substr($v, 5)];
                        } elseif ('*eq_int*:' === substr($v, 0, 9)) {
                            $c += ['$eq' => (int) substr($v, 9)];
                        } elseif ('*eq_dec*:' === substr($v, 0, 9)) {
                            $c += ['$eq' => (double) substr($v, 9)];
                        } elseif ('*regex*:' === substr($v, 0, 8)) {
                            if (preg_match('/^\//', substr($v, 8))) {
                                $c += ['$regex' => new \MongoRegex(substr($v, 8))];
                            } else {
                                $c += ['$regex' => substr($v, 8)];
                            }
                        } elseif ('*search_key*:' === substr($v, 0, 13)) {
                            $c += ['$regex' => str_replace('-', '.+', $this->getGeneratorService()->generate('search_key', substr($v, 13)))];
                        } elseif ('*near*:' === substr($v, 0, 7)) {
                            $_tokens = explode(' ', trim(substr($v, 7)));
                            $lng = 0.0;
                            $lat = 0.0;
                            $maxDistance = 10000; // 10 kms
                            $minDistance = 0; // 0 kms
                            if (isset($_tokens)) {
                                $lng = (double) array_shift($_tokens);
                            }
                            if (isset($_tokens)) {
                                $lat = (double) array_shift($_tokens);
                            }
                            if (isset($_tokens)) {
                                $maxDistance = (double) array_shift($_tokens);
                            }
                            if (isset($_tokens)) {
                                $minDistance = (double) array_shift($_tokens);
                            }
                            $c += [
                                '$near' => [
                                    '$geometry' => [
                                        'type' => 'Point',
                                        'coordinates' => [$lng, $lat],
                                    ],
                                    '$minDistance' => $minDistance,
                                    '$maxDistance' => $maxDistance,
                                ],
                            ];
                        } elseif ('*nearest*:' === substr($v, 0, 10)) {
                            $_tokens = explode(' ', trim(substr($v, 10)));
                            $lng = 0.0;
                            $lat = 0.0;
                            $maxDistance = 10000; // 10 kms
                            $minDistance = 0; // 0 kms
                            if (isset($_tokens)) {
                                $lng = (double) array_shift($_tokens);
                            }
                            if (isset($_tokens)) {
                                $lat = (double) array_shift($_tokens);
                            }
                            if (isset($_tokens)) {
                                $maxDistance = (double) array_shift($_tokens);
                            }
                            if (isset($_tokens)) {
                                $minDistance = (double) array_shift($_tokens);
                            }
                            $c += [
                                '$nearSphere' => [
                                    '$geometry' => [
                                        'type' => 'Point',
                                        'coordinates' => [$lng, $lat],
                                    ],
                                    '$minDistance' => $minDistance,
                                    '$maxDistance' => $maxDistance,
                                ],
                            ];
                        } elseif ('*within-circle*:' === substr($v, 0, 16)) {
                            $_tokens = explode(' ', trim(substr($v, 16)));
                            $lng = 0.0;
                            $lat = 0.0;
                            $radius = 10000; // 10 kms
                            if (isset($_tokens)) {
                                $lng = (double) array_shift($_tokens);
                            }
                            if (isset($_tokens)) {
                                $lat = (double) array_shift($_tokens);
                            }
                            if (isset($_tokens)) {
                                $radius = (double) array_shift($_tokens);
                            }
                            $c += [
                                '$geoWithin' => [
                                    '$centerSphere' => [
                                        [$lng, $lat],
                                        ($radius / 1000) / 6378.1,
                                    ],
                                ],
                            ];
                        } elseif ('*text*:' === substr($v, 0, 7)) {
                            $textCriteria = ['$search' => substr($v, 7)];
                        } elseif ('*where*:' === substr($v, 0, 8)) {
                            $c += ['$where' => substr($v, 8)];
                        } elseif ('*all*:' === substr($v, 0, 6)) {
                            $_a = trim(substr($v, 6));
                            if (strlen($_a)) {
                                $c += ['$all' => $this->prepareArrayValuesForField($k, array_map(function ($vv) {
                                    return $vv;
                                }, explode(',', $_a))), ];
                            }
                        } elseif ('*size*:' === substr($v, 0, 7)) {
                            $c += ['$size' => substr($v, 7)];
                        } elseif ('*all_int*:' === substr($v, 0, 10)) {
                            $c += ['$all' => array_map(function ($vv) {
                                return (int) $vv;
                            }, explode(',', substr($v, 10))), ];
                        } elseif ('*all_dec*:' === substr($v, 0, 10)) {
                            $c += ['$all' => array_map(function ($vv) {
                                return (double) $vv;
                            }, explode(',', substr($v, 10))), ];
                        } elseif ('*mod*:' === substr($v, 0, 5)) {
                            $c += ['$mod' => array_slice(array_map(function ($vv) {
                                return (int) $vv;
                            }, explode(',', substr($v, 5))), 0, 2), ];
                        } else {
                            $c = $this->prepareValueForField($k, $v);
                        }
                    } else {
                        $c = $this->prepareValueForField($k, $v);
                    }
                }
                if ([] != $c) {
                    $criteria[$k] = $c;
                } else {
                    unset($criteria[$k]);
                }
            } elseif (is_array($_v)) {
                $criteria[$k] = $this->prepareArrayValuesForField($k, $_v);
            }
        }
        if (null !== $textCriteria) {
            $criteria['$text'] = $textCriteria;
        }

        return $criteria;
    }
    /**
     * @param string $k
     * @param mixed  $v
     *
     * @return mixed|MongoId
     *
     * @throws \Exception
     */
    protected function prepareValueForField($k, $v)
    {
        if ('_id' === $k || 'id' === $k) {
            return $this->ensureMongoId($v);
        }

        return $v;
    }
    /**
     * @param string $k
     * @param array  $vs
     *
     * @return mixed[]|MongoId[]
     *
     * @throws \Exception
     */
    protected function prepareArrayValuesForField($k, $vs)
    {
        if ('_id' === $k || 'id' === $k) {
            return $this->ensureMongoId($vs);
        }

        return $vs;
    }
    /**
     * Ensure sorts are well formed (array).
     *
     * @param array|mixed $sorts
     *
     * @return array
     */
    protected function buildSorts($sorts)
    {
        $cleanedSorts = [];

        if (!is_array($sorts)) {
            return $cleanedSorts;
        }

        foreach ($sorts as $k => $v) {
            if (is_numeric($k)) {
                if (!is_string($v)) {
                    $v = (string) $v;
                }
                $cleanedSorts[$v] = 1;
            } else {
                $v = ((int) $v) === -1 ? -1 : 1;
                $cleanedSorts[$k] = $v;
            }
        }

        return $cleanedSorts;
    }
    /**
     * Ensure document data are well formed (array).
     *
     * @param array $data
     *
     * @return array
     */
    protected function buildData($data)
    {
        if (!is_array($data) || !count($data)) {
            return [];
        }
        if (isset($data['_id'])) {
            $data['_id'] = $this->ensureMongoId($data['_id']);
        }

        return $data;
    }
    /**
     * Ensure documents data are well formed (array).
     *
     * @param array $bulkData
     *
     * @return array
     */
    protected function buildBulkData($bulkData)
    {
        if (!is_array($bulkData) || !count($bulkData)) {
            return [];
        }

        foreach ($bulkData as $a => $b) {
            $bulkData[$a] = $this->buildData($b);
        }

        return $bulkData;
    }
}
