<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Closure;
use Exception;
use Itq\Common\Traits;

/**
 * Collection Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class CollectionService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\CriteriumServiceAwareTrait;
    /**
     * @param CriteriumService $criteriumService
     */
    public function __construct(CriteriumService $criteriumService)
    {
        $this->setCriteriumService($criteriumService);
        $this->setParameter(
            'operations',
            [
                'value' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ($item[$kk] === $vv);
                },
                'exists' => function ($item, $vv, $kk) {
                    return !(true === $vv && !isset($item[$kk])) && !(false === $vv && isset($item[$kk]));
                },
                'equals_int' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ((int) $item[$kk] === (int) $vv);
                },
                'equals_double' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ((double) $item[$kk] === (double) $vv);
                },
                'equals_bool' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ((bool) $item[$kk] === (bool) $vv);
                },
                'equals' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ($item[$kk] === $vv);
                },
                'different' => function ($item, $vv, $kk) {
                    return !isset($item[$kk]) || ($item[$kk] !== $vv);
                },
                'different_int' => function ($item, $vv, $kk) {
                    return !isset($item[$kk]) || ((int) $item[$kk] !== (int) $vv);
                },
                'different_double' => function ($item, $vv, $kk) {
                    return !isset($item[$kk]) || ((double) $item[$kk] !== (double) $vv);
                },
                'not_equals_double' => function ($item, $vv, $kk) {
                    return !isset($item[$kk]) || ((double) $item[$kk] !== (double) $vv);
                },
                'less_than' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ((double) $item[$kk] < (double) $vv);
                },
                'less_than_equals' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ((double) $item[$kk] <= (double) $vv);
                },
                'greater_than' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ((double) $item[$kk] > (double) $vv);
                },
                'greater_than_equals' => function ($item, $vv, $kk) {
                    return isset($item[$kk]) && ((double) $item[$kk] >= (double) $vv);
                },
            ]
        );
    }
    /**
     * @param array        $items
     * @param array        $criteria
     * @param array        $fields
     * @param Closure|null $eachCallback
     * @param array        $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function filter(&$items, $criteria = [], $fields = [], Closure $eachCallback = null, $options = [])
    {
        $realFields = null;

        if (!is_array($fields)) {
            $fields = [];
        }
        if (!is_array($criteria)) {
            $criteria = [];
        }
        if (0 >= count($items)) {
            return $this;
        }
        if (0 < count($criteria)) {
            $realCriteria = $this->getCriteriumService()->buildSetQuery('collection', $criteria);
            foreach ($items as $k => $v) {
                if ($this->isValidItem($v, $realCriteria)) {
                    continue;
                }
                unset($items[$k]);
            }
        }
        if (0 < count($fields)) {
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
     * @param mixed $item
     * @param array $criteria
     *
     * @return bool
     */
    public function isValidItem(&$item, $criteria)
    {
        if (!is_array($criteria)) {
            return true;
        }

        foreach (array_keys($criteria) as $k) {
            if (!$this->hasArrayParameterKey('operations', $k)) {
                continue;
            }
            if (!is_array($criteria[$k])) {
                $criteria[$k] = [];
            }
            /** @var Closure $closure */
            $closure = $this->getArrayParameterKey('operations', $k);
            foreach ($criteria[$k] as $kk => $vv) {
                if (false === $closure($item, $vv, $kk)) {
                    return false;
                }
            }
        }

        return true;
    }
    /**
     * @param array $items
     * @param int   $limit
     * @param int   $offset
     * @param array $options
     *
     * @return $this
     */
    public function paginate(&$items, $limit, $offset, $options = [])
    {
        if (!is_array($items) || 0 >= count($items)) {
            return $this;
        }

        if (is_numeric($offset) && $offset > 0) {
            $items = array_slice($items, $offset, (is_numeric($limit) && $limit > 0) ? $limit : null, true);
        } elseif (is_numeric($limit) && $limit > 0) {
            $items = array_slice($items, 0, $limit, true);
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
    public function sort(&$items, $sorts = [], $options = [])
    {
        if (!is_array($items) || 0 >= count($items)) {
            return $this;
        }

        $sorts = is_array($sorts) ? $sorts : [];

        uasort(
            $items,
            function ($a, $b) use ($sorts) {
                foreach ($sorts as $field => $direction) {
                    if (false === $direction || -1 === (int) $direction || 0 === (int) $direction || 'false' === $direction || null === $direction) {
                        if (!isset($a[$field])) {
                            if (!isset($b[$field])) {
                                continue;
                            }

                            return -1;
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
                            }

                            return 1;
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
            }
        );

        unset($options);

        return $this;
    }
}
