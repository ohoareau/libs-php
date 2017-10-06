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

use Itq\Common\Service\SdkService;

/**
 * SdkServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SdkServiceAwareTrait
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
     * @return SdkService
     */
    public function getSdkService()
    {
        return $this->getService('sdkService');
    }
    /**
     * @param SdkService $service
     *
     * @return $this
     */
    public function setSdkService(SdkService $service)
    {
        return $this->setService('sdkService', $service);
    }
}
