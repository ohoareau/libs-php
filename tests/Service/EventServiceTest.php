<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Service;

use Itq\Common\Service;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group event
 */
class EventServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\EventService
     */
    protected $s;
    /**
     * @var Service\ActionService|PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionService;
    /**
     * @var Service\ContextService|PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextService;
    /**
     *
     */
    public function setUp()
    {
        $this->actionService  = $this->getMockBuilder(Service\ActionService::class)->disableOriginalConstructor()->getMock();
        $this->contextService = $this->getMockBuilder(Service\ContextService::class)->disableOriginalConstructor()->getMock();
        $this->s              = new Service\EventService($this->actionService, $this->contextService);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
