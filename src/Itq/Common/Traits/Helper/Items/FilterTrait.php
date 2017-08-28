<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Helper\Items;

use Closure;
use Exception;

/**
 * Filter trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait FilterTrait
{
    /**
     * @param array   $items
     * @param array   $criteria
     * @param array   $fields
     * @param Closure $eachCallback
     * @param array   $options
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function filterItems(&$items, $criteria = [], $fields = [], Closure $eachCallback = null, $options = [])
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
                                throw $this->createFailedException('where criteria operator not available');
                            } elseif ('*all*:' === substr($v, 0, 6)) {
                                $a = trim(substr($v, 6));
                                if ($this->isNonEmptyString($a)) {
                                    $realCriteria['all'][$k] = array_map(function ($vv) {
                                        return $vv;
                                    }, explode(',', $a));
                                }
                            } elseif ('*size*:' === substr($v, 0, 7)) {
                                throw $this->createFailedException('size criteria operator not available');
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
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    abstract protected function createFailedException($msg, ...$params);
    /**
     * @param string|mixed $value
     *
     * @return bool
     */
    abstract protected function isNonEmptyString($value);
}
