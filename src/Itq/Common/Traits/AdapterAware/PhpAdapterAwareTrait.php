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

use Itq\Common\Adapter\PhpAdapterInterface;

/**
 * PhpAdapterAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PhpAdapterAwareTrait
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
     * @return PhpAdapterInterface
     */
    public function getPhpAdapter()
    {
        return $this->getService('phpAdapter');
    }
    /**
     * @param PhpAdapterInterface $service
     *
     * @return $this
     */
    public function setPhpAdapter(PhpAdapterInterface $service)
    {
        return $this->setService('phpAdapter', $service);
    }
}
