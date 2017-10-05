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

use Itq\Common\Service\ResponseService;

/**
 * ResponseServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ResponseServiceAwareTrait
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
     * @return ResponseService
     */
    public function getResponseService()
    {
        return $this->getService('responseService');
    }
    /**
     * @param ResponseService $service
     *
     * @return $this
     */
    public function setResponseService(ResponseService $service)
    {
        return $this->setService('responseService', $service);
    }
}
