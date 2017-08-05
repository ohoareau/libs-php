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
use Itq\Common\ClientProviderInterface;

use PHPUnit_Framework_TestCase;

/**
 * @author Olivier Hoareau <olivier@itiqiti.com>
 *
 * @group decoredClient
 */
class DecoratedClientServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\DecoratedClientService
     */
    protected $s;
    /**
     * @var ClientProviderInterface
     */
    protected $clientService;
    /**
     *
     */
    public function setUp()
    {
        $this->clientService = $this->getMockBuilder(ClientProviderInterface::class)->disableOriginalConstructor()->getMock();
        $this->s = new Service\DecoratedClientService($this->clientService);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
