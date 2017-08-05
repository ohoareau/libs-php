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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ServiceTrait
{
    use ExceptionThrowerTrait;
    use MissingMethodCatcherTrait;
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
     * @param mixed  $service
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
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getService($key)
    {
        if (!$this->hasService($key)) {
            throw $this->createRequiredException("Service %s not set", $key);
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
     * @param mixed  $default
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getParameter($key, $default = null)
    {
        $value = $this->getParameterIfExists($key, $default);

        if (null === $value) {
            throw $this->createRequiredException("Parameter %s not set", $key);
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
     *
     * @return $this
     *
     * @throws \Exception
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
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getArrayParameterKey($name, $key)
    {
        $this->checkArrayParameterKeyExist($name, $key);

        return $this->parameters[$name][$key];
    }
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     *
     * @throws \Exception
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
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function unsetArrayParameterKey($name, $key)
    {
        unset($this->parameters[$name][$key]);

        return $this;
    }
    /**
     * @param string $name
     * @return mixed
     *
     * @throws \Exception
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
    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        return $this->setService('eventDispatcher', $eventDispatcher);
    }
    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->getService('eventDispatcher');
    }
    /**
     * @param string $event
     *
     * @return bool
     */
    protected function hasListeners($event)
    {
        return $this->getEventDispatcher()->hasListeners($event);
    }
    /**
     * @param string $event
     * @param null   $data
     *
     * @return $this
     */
    protected function dispatch($event, $data = null)
    {
        $this->getEventDispatcher()->dispatch($event, $data instanceof Event ? $data : new GenericEvent($data));

        return $this;
    }
}
