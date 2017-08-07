<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\RuleType\Base;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\Plugin\Base\AbstractPlugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractRuleType extends AbstractPlugin
{
    use Traits\ServiceAware\ExpressionServiceAwareTrait;
    /**
     * @param Service\ExpressionService $expressionService
     */
    public function __construct(Service\ExpressionService $expressionService)
    {
        $this->setExpressionService($expressionService);
    }
    /**
     * @param array  $data
     * @param string $key
     * @param array  $config
     * @param array  $options
     *
     * @return bool
     */
    protected function skippedValue(array &$data, $key, array &$config, array $options = [])
    {
        return $this->skipped($data, $key, '=', '$eq', $config, $options)
            || $this->skipped($data, $key, '<', '$lt', $config, $options)
            || $this->skipped($data, $key, '<=', '$lte', $config, $options)
            || $this->skipped($data, $key, '>', '$gt', $config, $options)
            || $this->skipped($data, $key, '>=', '$gte', $config, $options)
            || $this->skipped($data, $key, '!=', '$ne', $config, $options)
        ;
    }
    /**
     * @param array $data
     * @param array $config
     * @param array $options
     *
     * @return bool
     */
    protected function skippedIf(array &$data, array &$config, array $options = [])
    {
        $options += ['ifKey' => 'if'];

        return isset($config[$options['ifKey']])
            && true !== (bool) $this->getExpressionService()->evaluate($config[$options['ifKey']], $data)
        ;
    }
    /**
     * @param array  $config
     * @param string $key
     * @param array  $data
     * @param array  $options
     *
     * @return float|null
     */
    protected function computedFloatValue(
        array &$config,
        $key,
        array &$data,
        /** @noinspection PhpUnusedParameterInspection */  array $options = []
    ) {
        if (isset($config[$key]) && is_numeric($config[$key])) {
            return (double) $config[$key];
        }

        if (isset($config[$key]) && '$' === substr($config[$key], 0, 1)) {
            return (double) $this->getExpressionService()->evaluate($config[$key], $data);
        }

        return null;
    }
    /**
     * @param array  $data
     * @param string $key
     * @param string $operation
     * @param mixed  $expected
     * @param array  $config
     * @param array  $options
     *
     * @return bool
     */
    protected function skipped(
        array &$data,
        $key,
        $operation,
        $expected,
        array &$config,
        /** @noinspection PhpUnusedParameterInspection */ array $options = []
    ) {
        if ('$' === $expected{0}) {
            $configKey = lcfirst(str_replace(' ', '', ucwords(str_replace('.', ' ', substr($expected, 1)))));
            if (!isset($config[$configKey])) {
                return false;
            }
            $expected = $config[$configKey];
            unset($configKey);
        }

        $v = $this->accessProperty($data, $key);

        switch ($operation) {
            case '=':
                return null !== $v && $v != $expected;
            case '>':
                return null !== $v && $v <= $expected;
            case '<':
                return null !== $v && $v >= $expected;
            case '>=':
                return null !== $v && $v < $expected;
            case '<=':
                return null !== $v && $v > $expected;
            case '!=':
                return null !== $v && $v == $expected;
            default:
                // unsupported operation

                return true;
        }
    }
    /**
     * @param array|object  $data
     * @param string        $key
     *
     * @return mixed|null
     */
    protected function accessProperty(&$data, $key)
    {
        if (false !== ($p = strpos($key, '.'))) {
            list ($keyPrefix, $subKey) = explode('.', $key, 2);
            if (is_object($data) && property_exists($data, $keyPrefix)) {
                return $this->accessProperty($data->$keyPrefix, $subKey);
            }
            if (is_array($data) && isset($data[$keyPrefix])) {
                return $this->accessProperty($data[$keyPrefix], $subKey);
            }

            return null;
        }

        if (is_object($data) && property_exists($data, $key)) {
            return $data->$key;
        }

        if (is_array($data) && isset($data[$key])) {
            return $data[$key];
        }

        return null;
    }
}
