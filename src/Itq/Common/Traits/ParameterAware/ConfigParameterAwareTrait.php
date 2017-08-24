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
 * Config Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ConfigParameterAwareTrait
{
    /**
     * @param mixed $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        return $this->setParameter('config', $config);
    }
    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getConfig()
    {
        return $this->getParameter('config');
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
