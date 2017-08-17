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
 * JobType Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class JobTypeService
{
    use Traits\ServiceTrait;
    use Traits\CallableBagTrait;
    /**
     * Register an job type for the specified name (replace if exist).
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
        return $this->registerCallableByType('jobType', $name, $callable, $options);
    }
    /**
     * Register an job type set for the specified name (replace if exist).
     *
     * @param string $name
     * @param array  $jobTypes
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function registerSet($name, array $jobTypes, array $options = [])
    {
        return $this->registerCallableSetByType('jobType', $name, $jobTypes, $options);
    }
    /**
     * Return the job type registered for the specified name.
     *
     * @param string $name
     *
     * @return callable
     *
     * @throws \Exception if no jobType registered for this name
     */
    public function get($name)
    {
        return $this->getCallableByType('jobType', $name);
    }
    /**
     * @param string $name
     * @param mixed  $params
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function execute($name, $params = [], array $options = [])
    {
        return $this->executeCallableByType('jobType', $name, [$params, ['jobType' => $name] + $options]);
    }
}
