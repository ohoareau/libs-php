<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Service\Base;

use Itq\Common\Traits;
use Itq\Common\Tests\Base\AbstractTestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractServiceTestCase extends AbstractTestCase
{

    /**
     * @return object|Traits\ServiceTrait|PHPUnit_Framework_MockObject_MockObject
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
    /**
     *
     */
    public function setUp()
    {
        if (empty($this->getMockedMethod())) {
            $this->setObject($this->instantiate());
        } else {
            $this->setObject(
                $this->getMockBuilder($this->getObjectClass())->setMethods($this->getMockedMethod())->setConstructorArgs($this->getConstructorArguments())->getMock()
            );
        }

        $this->initializer();
    }
    /**
     * @param string $type
     * @param string $pluginClass
     * @param array  $methods
     * @param string $getter
     * @param string $adder
     * @param string $optionalTypeForAdder
     * @param string $optionalSingleGetter
     * @param string $optionalGroupGetter
     */
    protected function handleTestPlugins($type, $pluginClass, array $methods, $getter, $adder, $optionalTypeForAdder = null, $optionalSingleGetter = null, $optionalGroupGetter = null)
    {
        $mock = $this->mocked($type, $pluginClass, $methods);

        $this->assertEquals([], $this->s()->$getter());
        if (null !== $optionalTypeForAdder) {
            $this->s()->$adder($optionalTypeForAdder, $mock);
            if (null !== $optionalGroupGetter) {
                $this->assertEquals([$optionalTypeForAdder => [$mock]], $this->s()->$getter());
            } else {
                $this->assertEquals([$optionalTypeForAdder => $mock], $this->s()->$getter());
            }
            if (null !== $optionalSingleGetter) {
                $this->assertEquals($mock, $this->s()->$optionalSingleGetter($optionalTypeForAdder));
            }
            if (null !== $optionalGroupGetter) {
                $this->assertEquals([$mock], $this->s()->$optionalGroupGetter($optionalTypeForAdder));
            }
        } else {
            $this->s()->$adder($mock);
            $this->assertEquals([$mock], $this->s()->$getter());
        }
    }
    /**
     * @return array
     */
    protected function getMockedMethod()
    {
        return [];
    }
    /**
     * mock service method, must been added into getMockedMethod
     *
     * @param string     $method
     * @param null|mixed $args
     * @param null|mixed $return
     */
    protected function mockMethodOnce($method, $args = null, $return = null)
    {
        if (false === in_array($method, $this->getMockedMethod())) {
            throw new \RuntimeException(sprintf("'%s' method not found into mocked method, add it to getMockedMethod()", $method));
        }

        $observer = $this->s()->expects($this->once())->method($method);

        if (null !== $args) {
            if (!is_array($args)) {
                $args = [$args];
            }
            $observer->with(...$args);
        }

        if (null !== $return) {
            $observer->will($this->returnValue($return));
        }
    }
}
