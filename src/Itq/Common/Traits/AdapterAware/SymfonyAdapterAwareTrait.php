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

use Itq\Common\Adapter\SymfonyAdapterInterface;

/**
 * SymfonyAdapterAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SymfonyAdapterAwareTrait
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
     * @return SymfonyAdapterInterface
     */
    public function getSymfonyAdapter()
    {
        return $this->getService('symfonyAdapter');
    }
    /**
     * @param SymfonyAdapterInterface $service
     *
     * @return $this
     */
    public function setSymfonyAdapter(SymfonyAdapterInterface $service)
    {
        return $this->setService('symfonyAdapter', $service);
    }
}
