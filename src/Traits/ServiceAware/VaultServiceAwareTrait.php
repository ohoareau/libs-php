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

use Itq\Common\Service\VaultService;

/**
 * VaultServiceAware trait.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
trait VaultServiceAwareTrait
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
     * @return VaultService
     */
    public function getVaultService()
    {
        return $this->getService('vault');
    }
    /**
     * @param VaultService $service
     *
     * @return $this
     */
    public function setVaultService(VaultService $service)
    {
        return $this->setService('vault', $service);
    }
}
