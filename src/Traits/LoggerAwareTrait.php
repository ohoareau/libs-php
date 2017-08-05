<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Psr\Log\LoggerInterface;

/**
 * LoggerAware trait.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
trait LoggerAwareTrait
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
     * @param LoggerInterface $service
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $service)
    {
        return $this->setService('logger', $service);
    }
    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->getService('logger');
    }
    /**
     * @param string $msg
     * @param string $level
     *
     * @return $this
     */
    protected function log($msg, $level = 'debug')
    {
        $this->getLogger()->log($level, $msg);

        return $this;
    }
}
