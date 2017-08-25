<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\AdapterAware;

use Itq\Common\Adapter\SystemAdapterInterface;

/**
 * SystemAdapterAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SystemAdapterAwareTrait
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
     * @return SystemAdapterInterface
     */
    public function getSystemAdapter()
    {
        return $this->getService('systemAdapter');
    }
    /**
     * @param SystemAdapterInterface $service
     *
     * @return $this
     */
    public function setSystemAdapter(SystemAdapterInterface $service)
    {
        return $this->setService('systemAdapter', $service);
    }
}
