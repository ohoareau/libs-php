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

use Itq\Common\InstanceProviderInterface;

/**
 * InstanceProviderAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait InstanceProviderAwareTrait
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
     * @param InstanceProviderInterface $service
     *
     * @return $this
     */
    public function setInstanceProvider(InstanceProviderInterface $service)
    {
        return $this->setService('instanceProvider', $service);
    }
    /**
     * @return InstanceProviderInterface
     */
    public function getInstanceProvider()
    {
        return $this->getService('instanceProvider');
    }
}
