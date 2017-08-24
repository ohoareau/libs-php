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
 * Enabled Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait MasterParameterAwareTrait
{
    /**
     * @param bool $state
     */
    public function setMaster($state)
    {
        $this->setBoolParameter('master', $state);
    }
    /**
     * @return bool
     */
    public function isMaster()
    {
        return $this->getBoolParameter('master');
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
