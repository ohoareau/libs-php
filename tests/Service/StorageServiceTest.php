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

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group storage
 */
class StorageServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\StorageService
     */
    protected $s;
    /**
     * @var EventDispatcherInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcher;
    /**
     *
     */
    public function setUp()
    {
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcher::class)->setMethods(['dispatch'])->getMock();
        $this->s = new Service\StorageService($this->eventDispatcher);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
