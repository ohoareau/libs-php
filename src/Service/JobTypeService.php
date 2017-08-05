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
use Itq\Common\Service;

/**
 * JobType Service.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
class JobTypeService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\CallableServiceAwareTrait;
    /**
     * @param Service\CallableService $callableService
     */
    public function __construct(Service\CallableService $callableService)
    {
        $this->setCallableService($callableService);
    }
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
        $this->getCallableService()->registerByType('jobType', $name, $callable, $options);

        return $this;
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
        $this->getCallableService()->registerSetByType('jobType', $name, $jobTypes, $options);

        return $this;
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
        return $this->getCallableService()->getByType('jobType', $name);
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
        return $this->getCallableService()->executeByType('jobType', $name, [$params, ['jobType' => $name] + $options]);
    }
}
