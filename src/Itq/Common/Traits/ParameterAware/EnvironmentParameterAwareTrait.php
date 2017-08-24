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
 * Environment Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait EnvironmentParameterAwareTrait
{
    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->getParameter('environment');
    }
    /**
     * @param string $environment
     *
     * @return $this
     */
    public function setEnvironment($environment)
    {
        return $this->setParameter('environment', $environment);
    }
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    abstract protected function setParameter($key, $value);
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    abstract protected function getParameter($key, $default = null);
}
