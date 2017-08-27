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

use Closure;
use Exception;

/**
 * Callable Bag Trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CallableBagTrait
{
    /**
     * @param string $name
     *
     * @return mixed
     */
    abstract protected function getArrayParameter($name);
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    abstract protected function setArrayParameterKey($name, $key, $value);
    /**
     * @param string $name
     * @param string $key
     *
     * @return mixed
     */
    abstract protected function getArrayParameterKey($name, $key);
    /**
     * @param string $name
     * @param string $key
     *
     * @return bool
     */
    abstract protected function hasArrayParameterKey($name, $key);
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    abstract protected function createRequiredException($msg, ...$params);
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    abstract protected function createUnexpectedException($msg, ...$params);
    /**
     * Return the list of callables by type.
     *
     * @param string $type
     *
     * @return array
     */
    protected function listCallablesByType($type)
    {
        return $this->getArrayParameter($type.'s');
    }
    /**
     * Register a callable for the specified name (replace if exist).
     *
     * @param string   $type
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function registerCallableByType($type, $name, $callable, array $options = [])
    {
        $this->checkCallable($callable);

        return $this->setArrayParameterKey(
            $type.'s',
            $name,
            ['type' => 'callable', 'callable' => $callable, 'options' => $options]
        );
    }
    /**
     * Register a callable set for the specified name (replace if exist).
     *
     * @param string $type
     * @param string $name
     * @param array  $subItems
     * @param array  $options
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function registerCallableSetByType($type, $name, array $subItems, array $options = [])
    {
        foreach ($subItems as $k => $subItem) {
            if (!isset($subItem['name'])) {
                throw $this->createRequiredException(
                    "Missing name for %s #%d in set '%s'",
                    $type,
                    $k,
                    $name
                );
            }
        }

        return $this->setArrayParameterKey(
            $type.'s',
            $name,
            ['type' => 'set', 'subItems' => $subItems, 'options' => $options]
        );
    }
    /**
     * Return the callable for the specified name.
     *
     * @param string $type
     * @param string $name
     *
     * @return array
     *
     * @throws Exception if none for this name
     */
    protected function getCallableByType($type, $name)
    {
        return $this->getArrayParameterKey($type.'s', $name);
    }
    /**
     * Test if the callable for the specified name exist
     *
     * @param string $type
     * @param string $name
     *
     * @return bool
     */
    protected function hasCallableByType($type, $name)
    {
        return $this->hasArrayParameterKey($type.'s', $name);
    }
    /**
     * @param string $type
     *
     * @return mixed
     */
    protected function findCallablesByType($type)
    {
        return $this->getArrayParameter($type.'s');
    }
    /**
     * @param string  $type
     * @param string  $name
     * @param array   $params
     * @param Closure $conditionCallable
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function executeCallableByType($type, $name, array $params = [], \Closure $conditionCallable = null)
    {
        $params  += ['ignoreOnException' => false];
        $callable = $this->getCallableByType($type, $name);
        $r        = null;
        $that     = $this;
        $map      = [
            'callable' => function ($callable, $params, $options) use ($that) {
                return $that->executeCallable($callable['callable'], $params, $options);
            },
            'set' => function ($callable, $params) use ($that, $type, $conditionCallable) {
                return $that->executeCallableListByType($type, $callable['subItems'], $params, $conditionCallable);
            },
        ];

        try {
            if (!isset($map[$callable['type']])) {
                throw $this->createUnexpectedException("Unsupported callable type '%s'", $callable['type']);
            }
            $closure = $map[$callable['type']];
            $r       = $closure(
                $callable,
                $params + (isset($callable['params']) ? $callable['params'] : []),
                isset($callable['options']) ? $callable['options'] : []
            );
        } catch (\Exception $e) {
            if (true !== $params['ignoreOnException']) {
                throw $e;
            }
        }

        return $r;
    }
    /**
     * @param callable $callable
     * @param array    $params
     * @param array    $options
     *
     * @return mixed
     */
    protected function executeCallable($callable, array $params = [], array $options = [])
    {
        $this->checkCallable($callable);

        unset($options);

        return call_user_func_array($callable, $params);
    }
    /**
     * @param string        $type
     * @param array         $callables
     * @param array|Closure $params
     * @param Closure       $conditionCallable
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function executeCallableListByType($type, array $callables, $params = [], \Closure $conditionCallable = null)
    {
        if (!($params instanceof \Closure)) {
            $originalParams = $params;
            $params = function ($callableParams) use ($originalParams) {
                return $originalParams + $callableParams;
            };
        }

        $i = 0;

        foreach ($callables as $callable) {
            if (!is_array($callable)) {
                $callable = [];
            }

            if (!isset($callable['name'])) {
                throw $this->createRequiredException('Missing %s name (step #%d)', $type, $i);
            }

            if (!isset($callable['params']) || !is_array($callable['params'])) {
                $callable['params'] = [];
            }

            $preComputedParams = $params($callable['params'], isset($callable['options']) ? $callable['options'] : [], true);

            if (null !== $conditionCallable) {
                if (true !== $conditionCallable($preComputedParams)) {
                    continue;
                }
            }

            $computedParams = $params($callable['params'], isset($callable['options']) ? $callable['options'] : []);

            $this->executeCallableByType($type, $callable['name'], $computedParams);

            $i++;
        }

        return $this;
    }
    /**
     * @param $value
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function checkCallable($value)
    {
        if (!is_callable($value)) {
            throw $this->createUnexpectedException('Not a valid callable');
        }

        return $this;
    }
}
