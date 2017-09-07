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

use Exception;

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
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     *
     * @throws Exception
     */
    abstract protected function getArrayParameterKeyIfExists($name, $key, $default = null);
    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    protected function getConfigValue($key, $defaultValue = null)
    {
        return $this->getArrayParameterKeyIfExists('config', $key, $defaultValue);
    }
}
