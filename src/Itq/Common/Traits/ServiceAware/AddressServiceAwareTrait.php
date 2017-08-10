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

use Itq\Common\Service\AddressService;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait AddressServiceAwareTrait
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
     * @return AddressService
     */
    public function getAddressService()
    {
        return $this->getService('address');
    }
    /**
     * @param AddressService $service
     *
     * @return $this
     */
    public function setAddressService(AddressService $service)
    {
        return $this->setService('address', $service);
    }
}
