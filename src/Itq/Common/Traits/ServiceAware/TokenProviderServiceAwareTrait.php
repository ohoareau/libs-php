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

use Itq\Common\Service\TokenProviderService;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait TokenProviderServiceAwareTrait
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
     * @return TokenProviderService
     */
    public function getTokenProviderService()
    {
        return $this->getService('tokenProviderService');
    }
    /**
     * @param TokenProviderService $service
     *
     * @return $this
     */
    public function setTokenProviderService(TokenProviderService $service)
    {
        return $this->setService('tokenProviderService', $service);
    }
}
