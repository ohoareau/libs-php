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

use Itq\Common\Service\TenantService;

/**
 * TenantServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait TenantServiceAwareTrait
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
     * @param TenantService $service
     *
     * @return $this
     */
    public function setTenantService(TenantService $service)
    {
        return $this->setService('tenantService', $service);
    }
    /**
     * @return TenantService
     */
    public function getTenantService()
    {
        return $this->getService('tenantService');
    }
}
