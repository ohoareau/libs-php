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

use Itq\Common\Service\SupervisionService;

/**
 * SupervisionServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SupervisionServiceAwareTrait
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
     * @return SupervisionService
     */
    public function getSupervisionService()
    {
        return $this->getService('supervision');
    }
    /**
     * @param SupervisionService $service
     *
     * @return $this
     */
    public function setSupervisionService(SupervisionService $service)
    {
        return $this->setService('supervision', $service);
    }
}
