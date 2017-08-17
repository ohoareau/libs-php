<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Base;

use Itq\Common\Traits;
use Itq\Common\RepositoryInterface;

/**
 * Abstract Doc Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractDocService
{
    use Traits\ServiceTrait;
    use Traits\LoggerAwareTrait;
    use Traits\ServiceAware\FormServiceAwareTrait;
    use Traits\ServiceAware\ModelServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\WorkflowServiceAwareTrait;
    use Traits\ServiceAware\BusinessRuleServiceAwareTrait;
    /**
     * @return int|null
     */
    abstract public function getExpectedTypeCount();
    /**
     * @param array $types
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setTypes(array $types)
    {
        $expectedTypeCount = $this->getExpectedTypeCount();

        if (null !== $expectedTypeCount && $expectedTypeCount !== count($types)) {
            throw $this->createUnexpectedException(
                "Model service must have exactly %d types (found: %d)",
                $expectedTypeCount,
                count($types)
            );
        }

        return $this->setParameter('types', $types);
    }
    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->getParameter('types');
    }
    /**
     * @param string $separator
     *
     * @return string
     */
    public function getFullType($separator = '.')
    {
        return join($separator, $this->getTypes());
    }
    /**
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->getService('repository');
    }
    /**
     * @param RepositoryInterface $repository
     *
     * @return $this
     */
    public function setRepository(RepositoryInterface $repository)
    {
        return $this->setService('repository', $repository);
    }
    /**
     * @param array $ids
     * @param array $options
     *
     * @return string
     */
    public function getRepoKey(array $ids = [], $options = [])
    {
        $options += ['pattern' => '%ss', 'skip' => 0, 'separator' => '.'];

        $key    = '';
        $types  = $this->getTypes();
        $toSkip = $options['skip'];
        $sep    = $options['separator'];

        array_shift($types);

        while (count($types)) {
            $type = array_shift($types);
            if (!$toSkip) {
                $key .= ($key ? $sep : '').sprintf($options['pattern'], $type);
            } else {
                $toSkip--;
                if ($toSkip) {
                    continue;
                }
            }

            if (!count($ids)) {
                if (!count($types)) {
                    break;
                }
                $id = 'unknown';
            } else {
                $id = array_shift($ids);
            }

            $this->checkRepoKeyTokenIsValid($id, $sep);

            $key .= ($key ? $sep : '').$id;
        }

        if (count($ids)) {
            foreach ($ids as $id) {
                $this->checkRepoKeyTokenIsValid($id, $sep);
                $key .= ($key ? $sep : '').$id;
            }
        }

        return $key;
    }
    /**
     * Test if specified document event has registered event listeners.
     *
     * @param string $event
     *
     * @return bool
     */
    protected function observed($event)
    {
        return $this->hasListeners($this->buildEventName($event));
    }
    /**
     * Build the full event name.
     *
     * @param string $event
     *
     * @return string
     */
    protected function buildEventName($event)
    {
        return join('.', $this->getTypes()).'.'.$event;
    }
    /**
     * @param mixed $bulkData
     * @param array $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function checkBulkData($bulkData, $options = [])
    {
        if (!is_array($bulkData)) {
            throw $this->createRequiredException('Missing bulk data');
        }

        if (!count($bulkData)) {
            throw $this->createRequiredException('No data to process');
        }

        unset($options);

        return $this;
    }
    /**
     * Return the underlying model class.
     *
     * @param string $alias
     *
     * @return string
     */
    protected function getModelClass($alias = null)
    {
        if (null !== $alias) {
            if ('.' === substr($alias, 0, 1)) {
                return $this->getModelClass().'\\'.substr($alias, 1);
            }

            return $alias;
        }

        return $this->getMetaDataService()->getModelClassForId(join('.', $this->getTypes()));
    }
    /**
     * Return a new instance of the model.
     *
     * @param array $options
     *
     * @return mixed
     */
    protected function createModelInstance($options = [])
    {
        if (isset($options['model']) && !is_bool($options['model'])) {
            if (is_object($options['model'])) {
                return $options['model'];
            }
            $class = $this->getModelClass($options['model']);
        } else {
            $class = $this->getModelClass();
        }

        return new $class();
    }
    /**
     * @param array $values
     * @param array $options
     *
     * @return array
     */
    protected function buildTypeVars($values, $options = [])
    {
        $vars = [];

        $options += ['suffix' => 'Id'];

        foreach ($this->getTypes() as $type) {
            if (!count($values)) {
                $value = null;
            } else {
                $value = array_shift($values);
            }
            $vars[$type.$options['suffix']] = $value;
        }

        return $vars;
    }
    /**
     * @param array  $data
     * @param string $mode
     * @param array  $options
     *
     * @return array
     */
    protected function getUnvalidableKeys(array $data, $mode, array $options)
    {
        $cleared = [];

        foreach ($data as $k => $v) {
            if (is_string($v) && false !== strpos($v, '*cleared*')) {
                $cleared[$k] = true;
            }
        }

        unset($mode);
        unset($options);

        return $cleared;
    }
    /**
     * @param string $mode
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     */
    protected function validateData(array $data = [], $mode = 'create', array $options = [])
    {
        return $this->getFormService()->validate($this->getFullType(), $mode, $data, $options + ['unvalidableKeys' => $this->getUnvalidableKeys($data, $mode, $options)]);
    }
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    protected function refreshModel($model, array $options = [])
    {
        return $this->getModelService()->refresh($model, $options);
    }
    /**
     * @param array  $data
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    protected function enrichUpdates($data, $class, array $options = [])
    {
        return $this->getModelService()->enrichUpdates($data, $class, $options);
    }
    /**
     * @param mixed $model
     * @param array $options
     *
     * @return mixed
     */
    protected function cleanModel($model, array $options = [])
    {
        return $this->getModelService()->clean($model, $options);
    }
    /**
     * Convert provided model (object) to an array.
     *
     * @param mixed $model
     * @param array $options
     *
     * @return array
     */
    protected function convertToArray($model, array $options = [])
    {
        return $this->getModelService()->convertObjectToArray($model, $options);
    }
    /**
     * Convert provided data (array) to a model.
     *
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    protected function convertToModel(array $data, $options = [])
    {
        return $this->getModelService()->populateObject($this->createModelInstance($options), $data, $options);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @throws \Exception
     */
    protected function restrictModel($doc, array $options = [])
    {
        $this->getModelService()->restrict($doc, $options);
    }
    /**
     * Convert provided data (mixed) to a model property.
     *
     * @param array  $data
     * @param string $propertyName
     * @param array  $options
     *
     * @return mixed
     */
    protected function convertToModelProperty($data, $propertyName, $options = [])
    {
        return $this->getModelService()->populateObjectProperty($this->createModelInstance($options), $data, $propertyName, $options);
    }
    /**
     * @return string
     */
    protected function getModelName()
    {
        return join('.', $this->getTypes());
    }
    /**
     * Prepare fields values
     *
     * @param array $fields
     *
     * @return array
     */
    protected function prepareFields(array $fields = [])
    {
        return $this->getModelService()->prepareFields($this->getModelClass(), $fields);
    }
    /**
     * @param array $items
     * @param int   $limit
     * @param int   $offset
     * @param array $options
     *
     * @return $this
     */
    protected function paginateItems(&$items, $limit, $offset, $options = [])
    {
        if (empty($items)) {
            return $this;
        }

        if (is_numeric($offset) && $offset > 0) {
            if (is_numeric($limit) && $limit > 0) {
                $items = array_slice($items, $offset, $limit, true);
            } else {
                $items = array_slice($items, $offset, null, true);
            }
        } else {
            if (is_numeric($limit) && $limit > 0) {
                $items = array_slice($items, 0, $limit, true);
            }
        }

        unset($options);

        return $this;
    }
    /**
     * @param array $items
     * @param array $sorts
     * @param array $options
     *
     * @return $this
     */
    protected function sortItems(&$items, $sorts = [], $options = [])
    {
        if (empty($items)) {
            return $this;
        }

        if (!is_array($sorts)) {
            $sorts = [];
        }

        uasort($items, function ($a, $b) use ($sorts) {
            foreach ($sorts as $field => $direction) {
                if (false === $direction || -1 === (int) $direction || 0 === (int) $direction || 'false' === $direction || null === $direction) {
                    if (!isset($a[$field])) {
                        if (!isset($b[$field])) {
                            continue;
                        } else {
                            return -1;
                        }
                    } elseif (!isset($b[$field])) {
                        continue;
                    }
                    $result = strnatcmp($b[$field], $a[$field]);

                    if ($result > 0) {
                        return $result;
                    }
                } else {
                    if (!isset($a[$field])) {
                        if (!isset($b[$field])) {
                            continue;
                        } else {
                            return 1;
                        }
                    } elseif (!isset($b[$field])) {
                        continue;
                    }
                    $result = strnatcmp($a[$field], $b[$field]);

                    if ($result > 0) {
                        return $result;
                    }
                }
            }

            return -1;
        });

        unset($options);

        return $this;
    }
    /**
     * @param array $array
     * @param array $ids
     * @param array $options
     *
     * @return array
     */
    protected function mutateArrayToRepoChanges($array, array $ids = [], $options = [])
    {
        $changes  = [];

        foreach ($array as $k => $v) {
            $changes[$this->mutateKeyToRepoChangesKey($k, $ids)] = $v;
        }

        unset($options);

        return $changes;
    }

    /**
     * @param string $key
     * @param array  $ids
     * @param array  $options
     *
     * @return string
     */
    protected function mutateKeyToRepoChangesKey($key, array $ids = [], array $options = [])
    {
        unset($options);

        if (0 >= strlen($key)) {
            return $this->getRepoKey($ids);
        }

        return sprintf('%s.%s', $this->getRepoKey($ids), $key);
    }
    /**
     * @param string $token
     * @param string $sep
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function checkRepoKeyTokenIsValid($token, $sep)
    {
        if (false !== strpos($token, $sep)) {
            throw $this->createMalformedException("Key token '%s' is invalid (found: %s)", $token, $sep);
        }

        if (0 === strlen($token)) {
            throw $this->createMalformedException('Key token is empty', $token, $sep);
        }

        return $this;
    }
    /**
     * Cast criteria values and restore value keys
     *
     * @param array $criteria
     *
     * @return array
     */
    protected function prepareCriteria(array $criteria = [])
    {
        foreach ($criteria as $criteriaKey => &$criteriaValue) {
            if ('$or' === $criteriaKey) {
                $criteria[$criteriaKey] = $this->prepareCriteria($criteriaValue);
                continue;
            }
            if (false !== strpos($criteriaKey, ':')) {
                unset($criteria[$criteriaKey]);
                $this->prepareCompositeCriteria($criteriaKey, $criteriaValue);
                $criteria[$criteriaKey] = $criteriaValue;
            }
        }

        return $criteria;
    }
    /**
     * Transform composite criteria to normal criteria
     *
     * A composite criteria use this pattern 'key:type' => 'value'
     *
     * @param $key
     * @param $value
     */
    protected function prepareCompositeCriteria(&$key, &$value)
    {
        list($key, $criteriaValueType) = explode(':', $key, 2);

        switch (trim($criteriaValueType)) {
            case 'int':
                $value = (int) $value;
                break;
            case 'string':
                $value = (string) $value;
                break;
            case 'bool':
                $value = (bool) $value;
                break;
            case 'array':
                $value = json_decode($value, true);
                break;
            case 'float':
                $value = (double) $value;
                break;
            default:
                break;
        }
    }
    /**
     * @param array    $items
     * @param array    $criteria
     * @param array    $fields
     * @param \Closure $eachCallback
     * @param array    $options
     *
     * @return $this
     */
    protected function filterItems(&$items, $criteria = [], $fields = [], \Closure $eachCallback = null, $options = [])
    {
        if (!is_array($fields)) {
            $fields = [];
        }
        if (!is_array($criteria)) {
            $criteria = [];
        }

        if (empty($items)) {
            return $this;
        }

        if (is_array($criteria) && count($criteria) > 0) {
            $realCriteria = [
                'value' => [],
                'exists' => [],
                'equals' => [],
                'equals_double' => [],
                'equals_int' => [],
                'equals_bool' => [],
                'different' => [],
                'different_int' => [],
                'different_double' => [],
                'not_equals_double' => [],
                'in' => [],
                'in_int' => [],
                'in_double' => [],
                'not_in' => [],
                'not_in_int' => [],
                'not_in_double' => [],
                'less_than_equals' => [],
                'less_than' => [],
                'greater_than_equals' => [],
                'greater_than' => [],
                'regex' => [],
                'search' => [],
                'all' => [],
                'all_int' => [],
                'all_double' => [],
                'mod' => [],
            ];

            foreach ($criteria as $k => $_v) {
                if (is_string($_v)) {
                    foreach (explode('*|*', $_v) as $v) {
                        if ('*' === substr($v, 0, 1)) {
                            if ('*notempty*' === $v) {
                                $realCriteria['exists'][$k] = true;
                            } elseif ('*empty*' === $v) {
                                $realCriteria['exists'][$k] = false;
                            } elseif ('*true*' === $v) {
                                $realCriteria['equals_bool'][$k] = true;
                            } elseif ('*false*' === $v) {
                                $realCriteria['equals_bool'][$k] = false;
                            } elseif ('*not*:' === substr($v, 0, 6)) {
                                $realCriteria['different'][$k] = substr($v, 6);
                            } elseif ('*ne*:' === substr($v, 0, 5)) {
                                $realCriteria['different'][$k] = substr($v, 5);
                            } elseif ('*not_int*:' === substr($v, 0, 10)) {
                                $realCriteria['different_int'][$k] = substr($v, 10);
                            } elseif ('*not_bool*:' === substr($v, 0, 11)) {
                                $realCriteria['equals_bool'][$k] = !((bool) substr($v, 11));
                            } elseif ('*not_dec*:' === substr($v, 0, 10)) {
                                $realCriteria['not_equals_double'][$k] = (bool) substr($v, 10);
                            } elseif ('*in*:' === substr($v, 0, 5)) {
                                $realCriteria['in'][$k] = explode(',', substr($v, 5));
                            } elseif ('*in_int*:' === substr($v, 0, 9)) {
                                $realCriteria['in_int'][$k] = array_map(function ($vv) {
                                    return (int) $vv;
                                }, explode(',', substr($v, 9)));
                            } elseif ('*in_dec*:' === substr($v, 0, 9)) {
                                $realCriteria['in_double'][$k] = array_map(function ($vv) {
                                    return (double) $vv;
                                }, explode(',', substr($v, 9)));
                            } elseif ('*nin*:' === substr($v, 0, 6)) {
                                $realCriteria['not_in'][$k] = explode(',', substr($v, 6));
                            } elseif ('*nin_int*:' === substr($v, 0, 10)) {
                                $realCriteria['not_in_int'][$k] = array_map(function ($vv) {
                                    return (int) $vv;
                                }, explode(',', substr($v, 10)));
                            } elseif ('*nin_dec*:' === substr($v, 0, 10)) {
                                $realCriteria['not_in_double'][$k] = array_map(function ($vv) {
                                    return (double) $vv;
                                }, explode(',', substr($v, 10)));
                            } elseif ('*lte*:' === substr($v, 0, 6)) {
                                $realCriteria['less_than_equals'][$k] = (double) substr($v, 6);
                            } elseif ('*lt*:' === substr($v, 0, 5)) {
                                $realCriteria['less_than'][$k] = (double) substr($v, 5);
                            } elseif ('*gte*:' === substr($v, 0, 6)) {
                                $realCriteria['greater_than_equals'][$k] = (double) substr($v, 6);
                            } elseif ('*gt*:' === substr($v, 0, 5)) {
                                $realCriteria['greater_than'][$k] = (double) substr($v, 5);
                            } elseif ('*eq*:' === substr($v, 0, 5)) {
                                $realCriteria['equals_double'][$k] = (double) substr($v, 5);
                            } elseif ('*eq_int*:' === substr($v, 0, 9)) {
                                $realCriteria['equals_int'][$k] = (int) substr($v, 9);
                            } elseif ('*eq_dec*:' === substr($v, 0, 9)) {
                                $realCriteria['equals_double'][$k] = (double) substr($v, 9);
                            } elseif ('*regex*:' === substr($v, 0, 8)) {
                                $realCriteria['regex'][$k] = substr($v, 8);
                            } elseif ('*text*:' === substr($v, 0, 7)) {
                                $realCriteria['search'][$k] = substr($v, 7);
                            } elseif ('*where*:' === substr($v, 0, 8)) {
                                throw new \RuntimeException("where criteria operator not available", 500);
                            } elseif ('*all*:' === substr($v, 0, 6)) {
                                $a = trim(substr($v, 6));
                                if (strlen($a)) {
                                    $realCriteria['all'][$k] = array_map(function ($vv) {
                                        return $vv;
                                    }, explode(',', $a));
                                }
                            } elseif ('*size*:' === substr($v, 0, 7)) {
                                throw new \RuntimeException("size criteria operator not available", 500);
                            } elseif ('*all_int*:' === substr($v, 0, 10)) {
                                $realCriteria['all_int'][$k] = array_map(function ($vv) {
                                    return (int) $vv;
                                }, explode(',', substr($v, 10)));
                            } elseif ('*all_dec*:' === substr($v, 0, 10)) {
                                $realCriteria['all_double'][$k] = array_map(function ($vv) {
                                    return (double) $vv;
                                }, explode(',', substr($v, 10)));
                            } elseif ('*mod*:' === substr($v, 0, 5)) {
                                $realCriteria['mod'][$k] = array_slice(array_map(function ($vv) {
                                    return (int) $vv;
                                }, explode(',', substr($v, 5))), 0, 2);
                            } else {
                                $realCriteria['value'][$k] = $v;
                            }
                        } else {
                            $realCriteria['value'][$k] = $v;
                        }
                    }
                } else {
                    $realCriteria['value'][$k] = $_v;
                }
            }
            foreach ($items as $k => $v) {
                if ($this->isValidItem($v, $realCriteria)) {
                    continue;
                }
                unset($items[$k]);
            }
        }

        $realFields = null;

        if (count($fields) > 0) {
            $realFields = is_numeric(key($fields)) ? array_fill_keys($fields, true) : $fields;
        }

        foreach ($items as $id => $item) {
            if ($eachCallback) {
                $item = $eachCallback($item);
            }
            if (null !== $realFields) {
                $item = array_intersect_key($item, $realFields);
                $items[$id] = $item;
            }
        }

        unset($options);

        return $this;
    }
    /**
     * @param $item
     * @param $criteria
     * @return bool
     */
    protected function isValidItem(&$item, $criteria)
    {
        foreach ($criteria['value'] as $kk => $vv) {
            if (!isset($item[$kk]) || $item[$kk] !== $vv) {
                return false;
            }
        }

        foreach ($criteria['exists'] as $kk => $vv) {
            if (true === $vv && !isset($item[$kk])) {
                return false;
            } elseif (false === $vv && isset($item[$kk])) {
                return false;
            }
        }

        foreach ($criteria['equals_int'] as $kk => $vv) {
            if (!isset($item[$kk]) || (int) $item[$kk] !== (int) $vv) {
                return false;
            }
        }

        foreach ($criteria['equals_double'] as $kk => $vv) {
            if (!isset($item[$kk]) || (double) $item[$kk] !== (double) $vv) {
                return false;
            }
        }

        foreach ($criteria['not_equals_double'] as $kk => $vv) {
            if (isset($item[$kk]) && (double) $item[$kk] === (double) $vv) {
                return false;
            }
        }

        foreach ($criteria['equals_bool'] as $kk => $vv) {
            if (!isset($item[$kk]) || (bool) $item[$kk] !== (bool) $vv) {
                return false;
            }
        }

        foreach ($criteria['equals'] as $kk => $vv) {
            if (!isset($item[$kk]) || $item[$kk] !== $vv) {
                return false;
            }
        }

        foreach ($criteria['different'] as $kk => $vv) {
            if (isset($item[$kk]) && $item[$kk] === $vv) {
                return false;
            }
        }

        foreach ($criteria['different_int'] as $kk => $vv) {
            if (isset($item[$kk]) && (int) $item[$kk] === (int) $vv) {
                return false;
            }
        }

        foreach ($criteria['different_double'] as $kk => $vv) {
            if (isset($item[$kk]) && (double) $item[$kk] === (double) $vv) {
                return false;
            }
        }

        foreach ($criteria['less_than'] as $kk => $vv) {
            if (!isset($item[$kk]) || (double) $item[$kk] >= (double) $vv) {
                return false;
            }
        }

        foreach ($criteria['less_than_equals'] as $kk => $vv) {
            if (!isset($item[$kk]) || (double) $item[$kk] > (double) $vv) {
                return false;
            }
        }

        foreach ($criteria['greater_than'] as $kk => $vv) {
            if (!isset($item[$kk]) || (double) $item[$kk] <= (double) $vv) {
                return false;
            }
        }

        foreach ($criteria['greater_than_equals'] as $kk => $vv) {
            if (!isset($item[$kk]) || (double) $item[$kk] < (double) $vv) {
                return false;
            }
        }

        return true;
    }
}
