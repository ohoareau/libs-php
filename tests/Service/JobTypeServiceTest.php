<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author Olivier Hoareau <olivier@phppro.fr>
 *
 * @group jobType
 */
class JobTypeServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\JobTypeService
     */
    protected $s;
    /**
     * @var Service\CallableService|PHPUnit_Framework_MockObject_MockObject
     */
    protected $callableService;
    /**
     *
     */
    public function setUp()
    {
        $this->callableService = $this->getMockBuilder(Service\CallableService::class)->disableOriginalConstructor()->getMock();
        $this->s = new Service\JobTypeService($this->callableService);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
    /**
     * @group unit
     */
    public function testRegister()
    {
        $callback = function () {
        };

        $this->callableService
            ->expects($this->once())
            ->method('registerByType')
            ->will($this->returnValue($this->callableService))
            ->with('jobType', 'test', $callback)
        ;

        $this->s->register('test', $callback);

        $this->callableService
            ->expects($this->once())
            ->method('getByType')
            ->will($this->returnValue(['type' => 'callable', 'callable' => $callback, 'options' => []]))
            ->with('jobType', 'test')
        ;

        $this->assertEquals(['type' => 'callable', 'callable' => $callback, 'options' => []], $this->s->get('test'));
    }
}
