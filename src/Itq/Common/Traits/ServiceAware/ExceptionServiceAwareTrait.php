<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\ExceptionService;

/**
 * ExceptionServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ExceptionServiceAwareTrait
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
     * @param ExceptionService $service
     *
     * @return $this
     */
    public function setExceptionService(ExceptionService $service)
    {
        return $this->setService('exceptionService', $service);
    }
    /**
     * @return ExceptionService
     */
    public function getExceptionService()
    {
        return $this->getService('exceptionService');
    }
}
