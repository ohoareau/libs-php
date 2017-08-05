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

/**
 * @author Olivier Hoareau <olivier@itiqiti.com>
 *
 * @group data
 */
class DataServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\DataService
     */
    protected $s;
    /**
     *
     */
    public function setUp()
    {
        $this->s = new Service\DataService();
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}
