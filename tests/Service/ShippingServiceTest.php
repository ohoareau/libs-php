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
 * @group shipping
 */
class ShippingServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\ShippingService
     */
    protected $s;
    /**
     * @var Service\DateService|PHPUnit_Framework_MockObject_MockObject
     */
    protected $date;
    /**
     *
     */
    public function setUp()
    {
        $this->date = $this->getMockBuilder(Service\DateService::class)->getMock();
        $this->s    = new Service\ShippingService($this->date);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
