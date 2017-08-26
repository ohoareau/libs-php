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

use Exception;

/**
 * Base trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait BaseTrait
{
    use ObjectTrait;
    use PhpEngineTrait;
    use ExceptionThrowerTrait;
    use MissingMethodCatcherTrait;
    use Helper\String\StringTrait;
    /**
     * @var array
     */
    protected $services = [];
    /**
     * @var array
     */
    protected $parameters = [];
    /**
     * @param string $key
     * @param object $service
     *
     * @return $this
     */
    protected function setService($key, $service)
    {
        $this->services[$key] = $service;

        return $this;
    }
    /**
     * @param string $key
     *
     * @return bool
     */
    protected function hasService($key)
    {
        return (bool) isset($this->services[$key]);
    }
    /**
     * @param string $key
     *
     * @return object
     *
     * @throws Exception
     */
    protected function getService($key)
    {
        if (!$this->hasService($key)) {
            throw $this->createRequiredException('Service %s not set', $key);
        }

        return $this->services[$key];
    }
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    protected function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }
    /**
     * @param string $key
     * @param bool   $state
     *
     * @return $this
     */
    protected function setBoolParameter($key, $state)
    {
        return $this->setParameter($key, (bool) $state);
    }
    /**
     * @param string $key
     *
     * @return $this
     */
    protected function unsetParameter($key)
    {
        unset($this->parameters[$key]);

        return $this;
    }
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getParameter($key, $default = null)
    {
        $value = $this->getParameterIfExists($key, $default);

        if (null === $value) {
            throw $this->createRequiredException('Parameter %s not set', $key);
        }

        return $value;
    }
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getParameterIfExists($key, $default = null)
    {
        if (!isset($this->parameters[$key])) {
            return $default;
        }

        return $this->parameters[$key];
    }
    /**
     * @param string $key
     * @param bool   $default
     *
     * @return bool
     */
    protected function getBoolParameter($key, $default = false)
    {
        return true === (bool) $this->getParameterIfExists($key, (bool) $default);
    }
    /**
     * @param string $name
     * @param string $key
     *
     * @return bool
     */
    protected function hasArrayParameterKey($name, $key)
    {
        return isset($this->parameters[$name]) && array_key_exists($key, $this->parameters[$name]);
    }
    /**
     * @param string $name
     * @param string $key
     * @param string $subKey
     *
     * @return bool
     */
    protected function hasArrayParameterSubKey($name, $key, $subKey)
    {
        return isset($this->parameters[$name]) && isset($this->parameters[$name][$key]) && array_key_exists($subKey, $this->parameters[$name][$key]);
    }
    /**
     * @param string $name
     * @param string $key
     *
     * @return bool
     */
    protected function hasNotNullArrayParameterKey($name, $key)
    {
        return isset($this->parameters[$name]) && array_key_exists($key, $this->parameters[$name]) && null !== $this->parameters[$name][$key];
    }
    /**
     * @param string $name
     * @param string $key
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function checkArrayParameterKeyExist($name, $key)
    {
        if (!$this->hasArrayParameterKey($name, $key)) {
            throw $this->createRequiredException("No '%s' in %s list", $key, $name);
        }

        return $this;
    }
    /**
     * @param string $name
     * @param string $key
     * @param string $subKey
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function checkArrayParameterSubKeyExist($name, $key, $subKey)
    {
        if (!$this->hasArrayParameterSubKey($name, $key, $subKey)) {
            throw $this->createRequiredException("No '%s' in %s of %s list", $subKey, $key, $name);
        }

        return $this;
    }
    /**
     * @param string $name
     * @param string $key
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getArrayParameterKey($name, $key)
    {
        $this->checkArrayParameterKeyExist($name, $key);

        return $this->parameters[$name][$key];
    }
    /**
     * @param string $name
     * @param string $key
     * @param string $subKey
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getArrayParameterSubKey($name, $key, $subKey)
    {
        $this->checkArrayParameterSubKeyExist($name, $key, $subKey);

        return $this->parameters[$name][$key][$subKey];
    }
    /**
     * @param string $name
     *
     * @return string[]
     */
    protected function getArrayParameterKeys($name)
    {
        return isset($this->parameters[$name]) && is_array($this->parameters[$name]) ? array_keys($this->parameters[$name]) : [];
    }
    /**
     * @param string $name
     * @param string $key
     *
     * @return string[]
     */
    protected function getArrayParameterSubKeys($name, $key)
    {
        return isset($this->parameters[$name]) && is_array($this->parameters[$name]) && isset($this->parameters[$name][$key]) && is_array($this->parameters[$name][$key]) ? array_keys($this->parameters[$name][$key]) : [];
    }
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getArrayParameterKeyIfExists($name, $key, $default = null)
    {
        $value = null;

        if ($this->hasArrayParameterKey($name, $key)) {
            $value = $this->parameters[$name][$key];
        }

        if (null === $value) {
            $value = $default;
        }

        return $value;
    }
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function setArrayParameterKey($name, $key, $value)
    {
        if (!isset($this->parameters[$name])) {
            $this->parameters[$name] = [];
        }

        if (!is_array($this->parameters[$name])) {
            throw $this->createMalformedException("Parameter '%s' is not a list", $name);
        }

        $this->parameters[$name][$key] = $value;

        return $this;
    }
    /**
     * @param string $name
     * @param string $key
     * @param string $subKey
     * @param mixed  $value
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function setArrayParameterSubKey($name, $key, $subKey, $value)
    {
        if (!isset($this->parameters[$name])) {
            $this->parameters[$name] = [];
        }

        if (!is_array($this->parameters[$name])) {
            throw $this->createMalformedException("Parameter '%s' is not a list", $name);
        }

        if (!isset($this->parameters[$name][$key])) {
            $this->parameters[$name][$key] = [];
        }

        if (!is_array($this->parameters[$name][$key])) {
            throw $this->createMalformedException("Item '%s' of parameter '%s' is not a list", $key, $name);
        }

        $this->parameters[$name][$key][$subKey] = $value;

        return $this;
    }
    /**
     * @param string $name
     * @param string $key
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function unsetArrayParameterKey($name, $key)
    {
        unset($this->parameters[$name][$key]);

        return $this;
    }
    /**
     * @param string $name
     *
     * @return array
     *
     * @throws Exception
     */
    protected function getArrayParameter($name)
    {
        $value = $this->getParameter($name, []);

        if (!is_array($value)) {
            $this->createMalformedException("Parameter '%s' is not a list", $name);
        }

        return $value;
    }
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $item
     *
     * @return $this
     */
    protected function pushArrayParameterKeyItem($name, $key, $item)
    {
        if (!isset($this->parameters[$name])) {
            $this->parameters[$name] = [];
        }

        if (!isset($this->parameters[$name][$key])) {
            $this->parameters[$name][$key] = [];
        }

        $this->parameters[$name][$key][] = $item;

        return $this;
    }
    /**
     * @param string $name
     * @param mixed  $item
     *
     * @return $this
     */
    protected function pushArrayParameterItem($name, $item)
    {
        if (!isset($this->parameters[$name])) {
            $this->parameters[$name] = [];
        }

        $this->parameters[$name][] = $item;

        return $this;
    }
    /**
     * @param string $name
     * @param string $key
     *
     * @return array
     */
    protected function getArrayParameterListKey($name, $key)
    {
        if (!isset($this->parameters[$name][$key])) {
            return [];
        }

        if (!is_array($this->parameters[$name][$key])) {
            return [];
        }

        return $this->parameters[$name][$key];
    }
}
