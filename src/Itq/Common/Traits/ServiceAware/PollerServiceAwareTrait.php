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

use Itq\Common\Service\PollerService;

/**
 * PollerServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PollerServiceAwareTrait
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
     * @return PollerService
     */
    public function getPollerService()
    {
        return $this->getService('pollerService');
    }
    /**
     * @param PollerService $service
     *
     * @return $this
     */
    public function setPollerService(PollerService $service)
    {
        return $this->setService('pollerService', $service);
    }
}
