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
 * @author Olivier Hoareau <olivier@itiqiti.com>
 *
 * @group documentBuilder
 */
class DocumentBuilderServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\DocumentBuilderService
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
        $this->s = new Service\DocumentBuilderService($this->callableService);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
