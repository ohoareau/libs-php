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
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group connection
 */
class ConnectionServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\ConnectionService
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
        $this->s = new Service\ConnectionService($this->callableService);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
