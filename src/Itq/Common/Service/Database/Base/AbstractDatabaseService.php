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

use DateTime;
use Iterator;
use Exception;
use Itq\Common\Event;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ConnectionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractDatabaseService implements Service\DatabaseServiceInterface
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\StorageServiceAwareTrait;
    use Traits\ServiceAware\CriteriumServiceAwareTrait;
    use Traits\ServiceAware\ConnectionServiceAwareTrait;
    /**
     * @var DateTime[]
     */
    protected $timers;
    /**
     * @param Service\CriteriumService  $criteriumService
     * @param Service\ConnectionService $connectionService
     * @param EventDispatcherInterface  $eventDispatcher
     *
     * @throws Exception
     */
    public function __construct(
        Service\CriteriumService $criteriumService,
        Service\ConnectionService $connectionService,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->timers = [];

        $this->setCriteriumService($criteriumService);
        $this->setConnectionService($connectionService);

        if (null !== $eventDispatcher) {
            $this->setEventDispatcher($eventDispatcher);
        }
        $this->setParameter('databaseType', $this->buildDatabaseType());

        $this->init();
    }
    /**
     * @param string $partition
     * @param string $name
     * @param array  $options
     *
     * @return void
     *
     * @throws Exception
     */
    public function dropIndex($partition, $name, $options = [])
    {
        throw $this->createNotYetImplementedException('dropIndex');
    }
    /**
     * @return string
     */
    public function getDatabaseType()
    {
        return $this->getParameter('databaseType');
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
     * @param array $params
     * @param array $options
     *
     * @return ConnectionInterface
     */
    protected function getConnection($params = [], $options = [])
    {
        return $this->getConnectionService()->getConnection($this->getDatabaseType(), $params, $options);
    }
    /**
     * @return float
     */
    protected function start()
    {
        $now = microtime(true);

        $this->timers[] = $now;

        return $now;
    }
    /**
     * @return float[]
     *
     * @throws Exception
     */
    protected function stop()
    {
        if (!count($this->timers)) {
            $this->start();
        }

        $endDate = microtime(true);

        return [array_pop($this->timers), $endDate];
    }
    /**
     * @param string      $collection
     * @param string      $operation
     * @param array|null  $criteria
     * @param array|null  $data
     * @param array|mixed $result
     * @param array       $params
     * @param Exception   $e
     *
     * @return $this
     */
    protected function logQuery($collection, $operation, $criteria, $data, $result, array $params, Exception $e = null)
    {
        list ($startDate, $endDate) = $this->stop();

        $operation = is_array($operation) ? $operation : [$operation, $operation];

        return $this->silentDispatch(
            'database.query.executed',
            new Event\DatabaseQueryEvent(
                $operation[0],
                sprintf(
                    'db.%s.%s(%s)',
                    $collection,
                    $operation[1],
                    ($criteria ? json_encode($criteria) : '').($data ? (($criteria ? ', ' : '').json_encode($data)) : '')
                ),
                ['collection' => $collection] +
                ($data ? ['data' => $data] : []) +
                ($criteria ? ['criteria' => $criteria] : []) +
                $params,
                $startDate,
                $endDate,
                $result,
                $e
            )
        );
    }
    /**
     * @param string $k
     * @param array  $vs
     *
     * @return mixed[]
     *
     * @throws Exception
     */
    protected function prepareArrayValuesForField($k, $vs)
    {
        if ('_id' === $k || 'id' === $k) {
            return $this->ensureId($vs);
        }

        return $vs;
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
        return $this->getCriteriumService()->buildSetQuery($this->getDatabaseType(), $criteria);
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
            $data['_id'] = $this->ensureId($data['_id']);
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
    /**
     * @param string $id
     *
     * @return mixed
     *
     * @throws Exception if malformed
     */
    protected function ensureId($id)
    {
        if ($this->isValidId($id)) {
            return $id;
        }
        if (is_array($id)) {
            foreach ($id as $k => $iid) {
                $id[$k] = $this->ensureId($iid);
            }

            return $id;
        }

        return $this->castId($id);
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isValidId($value)
    {
        return is_string($value);
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function castId($value)
    {
        return (string) $value;
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

        if ($this->hasStorageService()) {
            $this->getStorageService()->save(sprintf('/caches/db/%s/%s', $this->getDatabaseType(), $key), $value);
        }

        return $this;
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
        if (!$this->hasStorageService()) {
            return [null, null];
        }

        $cacheKey = sha1(serialize($keyData));
        $value    = $this->getStorageService()->read(sprintf('/caches/db/%s/%s', $this->getDatabaseType(), $cacheKey), ['defaultValue' => null]);

        if ($value instanceof Iterator) {
            $value->rewind();
        }

        return [$cacheKey, $value];
    }
    /**
     * @return string
     */
    protected function buildDatabaseType()
    {
        return lcfirst(
            preg_replace('/^(.+)DatabaseService$/', '\\1', basename(str_replace('\\', '/', get_class($this))))
        );
    }
    /**
     *
     */
    protected function init()
    {
    }
}
