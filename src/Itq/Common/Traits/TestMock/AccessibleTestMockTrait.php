<?php
/*
 * This file is part of the tests-ws package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\TestMock;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait AccessibleTestMockTrait
{
    /**
     * @return object
     */
    abstract public function o();
    /**
     * @param string $method
     * @param array  ...$args
     * @return mixed
     */
    public function invokeProtected($method, ...$args)
    {
        $m = $this->accessible($this->o(), $method);
        if (empty($args)) {
            return $m->invoke($this->o());
        }

        return $m->invokeArgs($this->o(), $args);
    }
    /**
     * @param mixed  $object
     * @param string $method
     *
     * @return \ReflectionMethod
     */
    protected function accessible($object, $method)
    {
        $method = new \ReflectionMethod(get_class($object), $method);
        $method->setAccessible(true);

        return $method;
    }
}
