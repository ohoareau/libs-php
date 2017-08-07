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

/**
 * RedisAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait RedisAwareTrait
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
     * @param \Redis $service
     *
     * @return $this
     */
    public function setRedis(\Redis $service)
    {
        return $this->setService('redis', $service);
    }
    /**
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->getService('redis');
    }
}
