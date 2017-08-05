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
 * @group storage
 */
class VaultServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\StorageService
     */
    protected $s;
    /**
     * @var Service\StorageService|PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;
    /**
     *
     */
    public function setUp()
    {
        $this->storage = $this->getMockBuilder(Service\StorageService::class)->disableOriginalConstructor()->getMock();
        $this->s = new Service\VaultService($this->storage);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
