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

use Itq\Common\Service\CallableService;

use PHPUnit_Framework_TestCase;

/**
 * @author Olivier Hoareau <olivier@itiqiti.com>
 *
 * @group callable
 */
class CallableServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CallableService
     */
    protected $s;
    /**
     *
     */
    public function setUp()
    {
        $this->s = new CallableService();
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
    /**
     * @group unit
     *
     * @return void
     */
    public function testRegisterByType()
    {
        $callable1 = function () {
            return 1;
        };
        $callable2 = function () {
            return 2;
        };

        $this->s->registerByType('type1', 'callable1', $callable1);
        $this->s->registerByType('type1', 'callable2', $callable2);

        $this->assertEquals(
            ['type' => 'callable', 'callable' => $callable1, 'options' => []],
            $this->s->getByType('type1', 'callable1')
        );

        $this->assertEquals(
            ['type' => 'callable', 'callable' => $callable2, 'options' => []],
            $this->s->getByType('type1', 'callable2')
        );
    }
}
