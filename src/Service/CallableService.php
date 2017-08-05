<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * Callable Service.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
class CallableService
{
    use Traits\ServiceTrait;
    /**
     * Return the list of callables.
     *
     * @param string $type
     *
     * @return array
     */
    public function listByType($type)
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
     * @throws \Exception
     */
    public function registerByType($type, $name, $callable, array $options = [])
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
     * @throws \Exception
     */
    public function registerSetByType($type, $name, array $subItems, array $options = [])
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
     * @throws \Exception if none for this name
     */
    public function getByType($type, $name)
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
    public function hasByType($type, $name)
    {
        return $this->hasArrayParameterKey($type.'s', $name);
    }
    /**
     * @param string $type
     *
     * @return mixed
     */
    public function findByType($type)
    {
        return $this->getArrayParameter($type.'s');
    }
    /**
     * @param string   $type
     * @param string   $name
     * @param array    $params
     * @param \Closure $conditionCallable
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function executeByType($type, $name, array $params = [], \Closure $conditionCallable = null)
    {
        $callable = $this->getByType($type, $name);

        $params += ['ignoreOnException' => false];

        $r = null;

        try {
            switch ($callable['type']) {
                case 'callable':
                    $r = $this->execute(
                        $callable['callable'],
                        $params + (isset($callable['params']) ? $callable['params'] : []),
                        isset($callable['options']) ? $callable['options'] : []
                    );
                    break;
                case 'set':
                    $r = $this->executeListByType($type, $callable['subItems'], $params, $conditionCallable);
                    break;
                default:
                    throw $this->createUnexpectedException("Unsupported callable type '%s'", $callable['type']);
            }
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
    public function execute($callable, array $params = [], array $options = [])
    {
        $this->checkCallable($callable);

        unset($options);

        return call_user_func_array($callable, $params);
    }
    /**
     * @param string         $type
     * @param array          $callables
     * @param array|\Closure $params
     * @param \Closure       $conditionCallable
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function executeListByType($type, array $callables, $params = [], \Closure $conditionCallable = null)
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

            $this->executeByType($type, $callable['name'], $computedParams);

            $i++;
        }

        return $this;
    }
    /**
     * @param $value
     *
     * @return $this
     * @throws \Exception
     */
    protected function checkCallable($value)
    {
        if (!is_callable($value)) {
            throw $this->createUnexpectedException('Not a valid callable');
        }

        return $this;
    }
}
