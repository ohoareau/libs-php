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
 * @group filesystem
 */
class FilesystemServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\FilesystemService
     */
    protected $s;
    /**
     * @var Service\SystemService|PHPUnit_Framework_MockObject_MockObject
     */
    protected $system;
    /**
     *
     */
    public function setUp()
    {
        $this->system = $this->getMockBuilder(Service\SystemService::class)->getMock();
        $this->s      = new Service\FilesystemService($this->system);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
