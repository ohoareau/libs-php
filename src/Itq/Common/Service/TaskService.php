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
 * Task Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TaskService
{
    use Traits\ServiceTrait;
    use Traits\CallableBagTrait;
    /**
     * Register an task for the specified name (replace if exist).
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($name, $callable, array $options = [])
    {
        return $this->registerCallableByType('task', $name, $callable, $options);
    }
    /**
     * Register an task set for the specified name (replace if exist).
     *
     * @param string $name
     * @param array  $tasks
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function registerSet($name, array $tasks, array $options = [])
    {
        return $this->registerCallableSetByType('task', $name, $tasks, $options);
    }
    /**
     * Return the task registered for the specified name.
     *
     * @param string $name
     *
     * @return callable
     *
     * @throws \Exception if no task registered for this name
     */
    public function get($name)
    {
        return $this->getCallableByType('task', $name);
    }
    /**
     * @param string $name
     * @param array  $params
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function execute($name, array $params = [], array $options = [])
    {
        return $this->executeCallableByType('task', $name, [$params, ['task' => $name] + $options]);
    }
}
