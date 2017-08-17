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

/**
 * Callable Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class CallableService
{
    use Traits\ServiceTrait;
    use Traits\CallableBagTrait;
    /**
     * Return the list of callables.
     *
     * @param string $type
     *
     * @return array
     */
    public function listByType($type)
    {
        return $this->listCallablesByType($type);
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
        return $this->registerCallableByType($type, $name, $callable, $options);
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
        return $this->registerCallableSetByType($type, $name, $subItems, $options);
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
        return $this->getCallableByType($type, $name);
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
        return $this->hasCallableByType($type, $name);
    }
    /**
     * @param string $type
     *
     * @return mixed
     */
    public function findByType($type)
    {
        return $this->findCallablesByType($type);
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
        return $this->executeCallableByType($type, $name, $params, $conditionCallable);
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
        return $this->executeCallable($callable, $params, $options);
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
        return $this->executeCallableListByType($type, $callables, $params, $conditionCallable);
    }
}
