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

use Symfony\Component\Routing\RouterInterface;

/**
 * RouterAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait RouterAwareTrait
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
     * @param RouterInterface $router
     *
     * @return $this
     */
    public function setRouter(RouterInterface $router)
    {
        return $this->setService('router', $router);
    }
    /**
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->getService('router');
    }
}
