<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Itq\Common\ClientProviderInterface;

/**
 * ClientProviderAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ClientProviderAwareTrait
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
     * @param ClientProviderInterface $service
     *
     * @return $this
     */
    public function setClientProvider(ClientProviderInterface $service)
    {
        return $this->setService('clientProvider', $service);
    }
    /**
     * @return ClientProviderInterface
     */
    public function getClientProvider()
    {
        return $this->getService('clientProvider');
    }
}
