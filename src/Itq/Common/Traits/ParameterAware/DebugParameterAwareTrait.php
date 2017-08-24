<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ParameterAware;

/**
 * Debug Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DebugParameterAwareTrait
{
    /**
     * @param bool $state
     */
    public function setDebug($state)
    {
        $this->setBoolParameter('debug', $state);
    }
    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->getBoolParameter('debug');
    }
    /**
     * @param string $key
     * @param mixed  $state
     *
     * @return $this
     */
    abstract protected function setBoolParameter($key, $state);
    /**
     * @param string $key
     * @param bool   $default
     *
     * @return bool
     */
    abstract protected function getBoolParameter($key, $default = false);
}
