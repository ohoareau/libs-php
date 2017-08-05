<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\JobTypeService;

/**
 * JobTypeServiceAware trait.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
trait JobTypeServiceAwareTrait
{
    /**
     * @param string $key
     * @param mixed  $service
     *
     * @return $this
     */
    protected abstract function setService($key, $service);
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getService($key);
    /**
     * @return JobTypeService
     */
    public function getJobTypeService()
    {
        return $this->getService('jobType');
    }
    /**
     * @param JobTypeService $service
     *
     * @return $this
     */
    public function setJobTypeService(JobTypeService $service)
    {
        return $this->setService('jobType', $service);
    }
}
