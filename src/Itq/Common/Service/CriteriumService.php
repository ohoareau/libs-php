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

use Itq\Common\Traits;
use Itq\Common\Plugin\CriteriumTypeInterface;

/**
 * Criterium Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class CriteriumService
{
    use Traits\ServiceTrait;
    /**
     * @param string                 $set
     * @param string                 $type
     * @param CriteriumTypeInterface $criteriumType
     *
     * @return $this
     */
    public function addSetCriteriumType($set, $type, CriteriumTypeInterface $criteriumType)
    {
        return $this->setArrayParameterSubKey('setCriteriumTypes', $set, $type, $criteriumType);
    }
    /**
     * @param string $set
     *
     * @return CriteriumTypeInterface[]
     */
    public function getSetCriteriumTypes($set)
    {
        return $this->getArrayParameterListKey('setCriteriumTypes', $set);
    }
    /**
     * @param string $set
     * @param string $type
     *
     * @return CriteriumTypeInterface
     */
    public function getSetCriteriumType($set, $type)
    {
        return $this->getArrayParameterSubKey('setCriteriumTypes', $set, $type);
    }
    /**
     * @param string $set
     * @param string $type
     *
     * @return bool
     */
    public function hasSetCriteriumType($set, $type)
    {
        return $this->hasArrayParameterSubKey('setCriteriumTypes', $set, $type);
    }
    /**
     * @param string $set
     *
     * @return string[]
     */
    public function getSetCriteriumTypeNames($set)
    {
        return $this->getArrayParameterSubKeys('setCriteriumTypes', $set);
    }
    /**
     * @param string $set
     * @param mixed  $criteria
     *
     * @return array
     */
    public function buildSetQuery($set, $criteria)
    {
        if (!is_array($criteria)) {
            return [];
        }

        if (isset($criteria['$or']) && is_array($criteria['$or'])) {
            foreach ($criteria['$or'] as $a => $b) {
                if (isset($b['_id'])) {
                    list ($local) = $this->executeSetCriteriumType($set, 'default', $b['_id'], '_id');
                    $criteria['$or'][$a]['_id'] = $local;
                    unset($local);
                }
            }
        }

        foreach ($criteria as $k => $_v) {
            unset($criteria[$k]);
            if (is_string($_v)) {
                $c = [];
                foreach (explode('*|*', $_v) as $v) {
                    list($localCriteria, $globalCriteria) = $this->buildSetCriterium($set, $v, $k);
                    if (is_array($localCriteria)) {
                        $c += $localCriteria;
                    } else {
                        $c = $localCriteria;
                    }
                    if (is_array($globalCriteria)) {
                        $criteria += $globalCriteria;
                    }
                    unset($localCriteria, $globalCriteria);
                }
                if ([] != $c) {
                    $criteria[$k] = $c;
                }
            } elseif (is_array($_v)) {
                list ($local, $global) = $this->executeSetCriteriumType($set, 'default', $_v, $k);
                if (!is_array($local) || [] !== $local) {
                    $criteria[$k] = $local;
                }
                if (is_array($global)) {
                    $criteria += $global;
                }
                unset($local, $global);
            }
        }

        return $criteria;
    }
    /**
     * @param string $set
     * @param string $v
     * @param string $k
     * @param array  $options
     *
     * @return array
     */
    public function buildSetCriterium($set, $v, $k, array $options = [])
    {
        $code    = 'default';
        $value   = trim($v);

        if ('*' === substr($value, 0, 1)) {
            $matches = null;
            if (0 < preg_match('/^\*([^\:]+)\*(.*)$/', $value, $matches)) {
                $testCode  = trim($matches[1]);
                $testValue = trim($matches[2]);
                if (isset($testValue{0}) && ':' === $testValue{0}) {
                    $testValue = trim(substr($testValue, 1));
                }
                if ($this->hasSetCriteriumType($set, $testCode)) {
                    $code  = $testCode;
                    $value = $testValue;
                }
            }
        }

        return $this->executeSetCriteriumType($set, $code, '' === $value ? null : $value, $k, $options);
    }
    /**
     * @param string $set
     * @param string $code
     * @param mixed  $value
     * @param string $k
     * @param array  $options
     *
     * @return array
     */
    protected function executeSetCriteriumType($set, $code, $value, $k, array $options = [])
    {
        $result = $this->getSetCriteriumType($set, $code)->build($value, $k, ['criterium' => $code] + $options);
        $local  = null;
        $global = null;

        if (is_array($result)) {
            if (count($result) > 0) {
                $local = array_shift($result);
            }
            if (count($result) > 0) {
                $global = array_shift($result);
            }
        }

        return [$local, $global];
    }
}
