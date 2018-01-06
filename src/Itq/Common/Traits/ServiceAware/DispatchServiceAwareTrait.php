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

use Itq\Common\Service\DispatchService;

/**
 * DispatchServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DispatchServiceAwareTrait
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
     * @return DispatchService
     */
    public function getDispatchService()
    {
        return $this->getService('dispatchService');
    }
    /**
     * @param DispatchService $service
     *
     * @return $this
     */
    public function setDispatchService(DispatchService $service)
    {
        return $this->setService('dispatchService', $service);
    }
}
