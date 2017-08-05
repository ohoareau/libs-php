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

use Itq\Common\Service\ShippingService;

/**
 * ShippingServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ShippingServiceAwareTrait
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
     * @param ShippingService $service
     *
     * @return $this
     */
    public function setShippingService(ShippingService $service)
    {
        return $this->setService('shipping', $service);
    }
    /**
     * @return ShippingService
     */
    public function getShippingService()
    {
        return $this->getService('shipping');
    }
}
