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

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_MockObject_MockBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait MockTrait
{
    /**
     * @var array
     */
    protected $mocks;
    /**
     * @param string $className
     *
     * @return PHPUnit_Framework_MockObject_MockBuilder
     */
    abstract public function getMockBuilder($className);
    /**
     * @param string            $name
     * @param null|string|mixed $class
     * @param null|array        $methods
     *
     * @return mixed|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mocked($name, $class = null, $methods = null)
    {
        if (!isset($this->mocks[$name])) {
            $this->mocks[$name] = is_object($class)
                ? $class
                : $this->getMockBuilder($class)->disableOriginalConstructor()->setMethods($methods ?: [])->getMock()
            ;
        }

        return $this->mocks[$name];
    }
}
