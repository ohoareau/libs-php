<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group archiver
 */
class ArchiverServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\ArchiverService
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
        $this->s = new Service\ArchiverService($this->callableService);
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
            ->with('archiver', 'test', $callback)
        ;

        $this->s->register('test', $callback);

        $this->callableService
            ->expects($this->once())
            ->method('getByType')
            ->will($this->returnValue(['type' => 'callable', 'callable' => $callback, 'options' => []]))
            ->with('archiver', 'test')
        ;

        $this->assertEquals(['type' => 'callable', 'callable' => $callback, 'options' => []], $this->s->get('test'));
    }
}
