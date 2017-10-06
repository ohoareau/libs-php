<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Traits\ServiceAware\Base;

use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractServiceAwareTraitTestCase extends AbstractTestCase
{
    /**
     * @return mixed
     */
    public function t()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
    /**
     * @group unit
     */
    public function testXetters()
    {
        $getterMethod = $this->buildServiceGetterMethod();
        $setterMethod = $this->buildServiceSetterMethod();
        $testerMethod = $this->buildServiceTesterMethod();
        $mock         = $this->buildServiceMockObject();

        $this->t()->expects($this->once())->method('setService')->willReturn($this->t())->with(lcfirst($this->getServiceClassName()), $mock);
        $this->t()->expects($this->once())->method('getService')->willReturn($mock)->with(lcfirst($this->getServiceClassName()));

        if (method_exists($this->t(), $testerMethod)) {
            $this->t()->expects($this->once())->method('hasService')->willReturn(true)->with(lcfirst($this->getServiceClassName()));
        }

        $this->assertSame($this->t(), $this->t()->$setterMethod($mock));
        $this->assertSame($mock, $this->t()->$getterMethod());

        if (method_exists($this->t(), $testerMethod)) {
            $this->assertEquals(true, $this->t()->$testerMethod());
        }
    }
    /**
     * @return string
     */
    protected function buildServiceGetterMethod()
    {
        return sprintf('get%s', $this->getServiceClassName());
    }
    /**
     * @return string
     */
    protected function buildServiceSetterMethod()
    {
        return sprintf('set%s', $this->getServiceClassName());
    }
    /**
     * @return string
     */
    protected function buildServiceTesterMethod()
    {
        return sprintf('has%s', $this->getServiceClassName());
    }
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildServiceMockObject()
    {
        $class = preg_replace('/ServiceAware\\\/', '', preg_replace('/ServiceAwareTrait$/', 'Service', preg_replace('/Traits/', 'Service', $this->getObjectClass())));

        if (interface_exists($class.'Interface')) {
            $class .= 'Interface';
        }

        return $this->mocked('service', $class);
    }
    /**
     * @return string
     */
    protected function getServiceClassName()
    {
        return preg_replace('/ServiceAwareTrait$/', 'Service', basename(str_replace('\\', '/', $this->getObjectClass())));
    }
    /**
     * @param null|array $args
     *
     * @return object
     */
    protected function instantiate($args = null)
    {
        return $this->getMockForTrait($this->getObjectClass());
    }
}
