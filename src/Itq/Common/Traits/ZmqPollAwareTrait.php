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

use ZMQPoll;

/**
 * ZMQPollerAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ZmqPollAwareTrait
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
     * @param ZMQPoll $service
     *
     * @return $this
     */
    public function setZMQPoll(ZMQPoll $service)
    {
        return $this->setService('ZMQPoll', $service);
    }
    /**
     * @return ZMQPoll
     */
    public function getZMQPoll()
    {
        return $this->getService('ZMQPoll');
    }
}
