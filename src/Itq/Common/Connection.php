<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class Connection implements ConnectionInterface
{
    /**
     * @var mixed
     */
    protected $backend;
    /**
     * @var array
     */
    protected $parameters;
    /**
     * @param mixed $backend
     * @param array $params
     */
    public function __construct($backend, $params = [])
    {
        $this->backend    = $backend;
        $this->parameters = $params;
    }
    /**
     * @return mixed
     */
    public function getBackend()
    {
        return $this->backend;
    }
    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     * @param string $name
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getParameter($name, $defaultValue = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $defaultValue;
    }
    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }
}
