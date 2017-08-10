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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group customizer
 */
class CustomizerServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\CustomizerService
     */
    protected $s;
    /**
     *
     */
    public function setUp()
    {
        $this->s = new Service\CustomizerService();
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
}