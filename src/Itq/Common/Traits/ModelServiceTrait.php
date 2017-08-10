<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Itq\Common\RepositoryInterface;

/**
 * ModelServiceTrait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelServiceTrait
{
    use FilterItemsTrait;
    use ModelServiceHelperTrait;
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
}
