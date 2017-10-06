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

use Itq\Common\Service\SymfonyService;

/**
 * SymfonyServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SymfonyServiceAwareTrait
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
     * @return SymfonyService
     */
    public function getSymfonyService()
    {
        return $this->getService('symfonyService');
    }
    /**
     * @param SymfonyService $service
     *
     * @return $this
     */
    public function setSymfonyService(SymfonyService $service)
    {
        return $this->setService('symfonyService', $service);
    }
}
