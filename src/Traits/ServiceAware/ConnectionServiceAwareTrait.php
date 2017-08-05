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

use Itq\Common\Service\ConnectionService;

/**
 * ConnectionServiceAware trait.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
trait ConnectionServiceAwareTrait
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
     * @param ConnectionService $service
     *
     * @return $this
     */
    public function setConnectionService(ConnectionService $service)
    {
        return $this->setService('connection', $service);
    }
    /**
     * @return ConnectionService
     */
    public function getConnectionService()
    {
        return $this->getService('connection');
    }
}
