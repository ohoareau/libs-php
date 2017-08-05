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

use Itq\Common\ErrorManagerInterface;

/**
 * ErrorManager Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ErrorManagerAwareTrait
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
     * @param string $key
     *
     * @return bool
     */
    protected abstract function hasService($key);
    /**
     * @param ErrorManagerInterface $service
     *
     * @return $this
     */
    public function setErrorManager(ErrorManagerInterface $service)
    {
        return $this->setService('errorManager', $service);
    }
    /**
     * @return ErrorManagerInterface
     */
    public function getErrorManager()
    {
        return $this->getService('errorManager');
    }
    /**
     * @return bool
     */
    public function hasErrorManager()
    {
        return $this->hasService('errorManager');
    }
}
