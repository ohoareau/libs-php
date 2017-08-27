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
 * Php Engine trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PhpEngineTrait
{
    /**
     * @param object $object
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function callPhpMethod($object, $method, array $args = [])
    {
        $this->checkPhpMethod($object, $method);

        return call_user_func_array([$object, $method], $args);
    }
    /**
     * @param callable $callable
     * @param array    $args
     *
     * @return mixed
     */
    protected function callPhpCallable($callable, array $args = [])
    {
        $this->checkPhpCallable($callable);

        return call_user_func_array($callable, $args);
    }
    /**
     * @param callable|mixed $callable
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function checkPhpCallable($callable)
    {
        if (!$this->isPhpCallable($callable)) {
            throw $this->createMalformedException('Not a valid callable');
        }

        return $this;
    }
    /**
     * @param callable|mixed $callable
     *
     * @return bool
     */
    protected function isPhpCallable($callable)
    {
        return true === is_callable($callable);
    }
    /**
     * @param object $object
     * @param string $method
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function checkPhpMethod($object, $method)
    {
        $this->checkPhpObject($object);

        if (!$this->hasPhpMethod($object, $method)) {
            throw $this->createRequiredException("No method '%s' on object of class '%s'", $method, get_class($object));
        }

        return $this;
    }
    /**
     * @param mixed|object $object
     * @param string       $method
     * @param bool         $strict
     *
     * @return bool
     */
    protected function hasPhpMethod($object, $method, $strict = false)
    {
        return (true !== $strict || method_exists($object, '__call')) && method_exists($object, $method);
    }
    /**
     * @param mixed $object
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function checkPhpObject($object)
    {
        if (!is_object($object)) {
            throw $this->createMalformedException('Not an object');
        }

        return $this;
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    protected function hasPhpFunction($name)
    {
        return true === function_exists($name);
    }
}
