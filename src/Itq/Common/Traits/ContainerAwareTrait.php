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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ContainerAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ContainerAwareTrait
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
     * @param ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container = null)
    {
        return $this->setService('container', $container);
    }
    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->getService('container');
    }
}
