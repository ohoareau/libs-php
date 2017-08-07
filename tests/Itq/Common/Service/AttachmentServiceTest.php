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
 */
class AttachmentServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\AttachmentService
     */
    protected $s;
    /**
     * @var Service\GeneratorService|PHPUnit_Framework_MockObject_MockObject
     */
    protected $generator;
    /**
     *
     */
    public function setUp()
    {
        $this->generator = $this->getMockBuilder(Service\GeneratorService::class)->disableOriginalConstructor()->getMock();
        $this->s = new Service\AttachmentService($this->generator);
    }
    /**
     * @group unit
     * @group attachment
     */
    public function testBuild()
    {
        $this->generator->expects($this->once())->method('generate')->will($this->returnValue('result'));

        $this->assertEquals(
            ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('result')],
            $this->s->build(['name' => 'test.pdf', 'generator' => 'test'])
        );
    }
}
