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
 * Master Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait EnabledParameterAwareTrait
{
    /**
     * @param bool $state
     */
    public function setEnabled($state)
    {
        $this->setBoolParameter('enabled', $state);
    }
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getBoolParameter('enabled');
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
