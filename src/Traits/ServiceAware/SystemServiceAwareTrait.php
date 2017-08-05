<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\SystemService;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SystemServiceAwareTrait
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
     * @return SystemService
     */
    public function getSystemService()
    {
        return $this->getService('system');
    }
    /**
     * @param SystemService $service
     *
     * @return $this
     */
    public function setSystemService(SystemService $service)
    {
        return $this->setService('system', $service);
    }
}
