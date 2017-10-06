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

use Itq\Common\Service\RequestService;

/**
 * RequestServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait RequestServiceAwareTrait
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
     * @param RequestService $service
     *
     * @return $this
     */
    public function setRequestService(RequestService $service)
    {
        return $this->setService('requestService', $service);
    }
    /**
     * @return RequestService
     */
    public function getRequestService()
    {
        return $this->getService('requestService');
    }
}
