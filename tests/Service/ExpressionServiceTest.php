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

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group expression
 */
class ExpressionServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\ExpressionService
     */
    protected $s;
    /**
     * @var EngineInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $templatingService;
    /**
     * @var ExpressionLanguage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $expressionLanguage;
    /**
     *
     */
    public function setUp()
    {
        $this->templatingService  = $this->getMockBuilder(EngineInterface::class)->disableOriginalConstructor()->getMock();
        $this->expressionLanguage = $this->getMockBuilder(ExpressionLanguage::class)->disableOriginalConstructor()->getMock();
        $this->s = new Service\ExpressionService($this->templatingService, $this->expressionLanguage);
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
    }
    /**
     * @group integ
     */
    public function testEvaluateExpressionLanguage()
    {
        $this->s->setExpressionLanguage(new ExpressionLanguage());

        $vars = ['a' => [1, 2], 'b' => 5, 'c' => 2];

        $this->assertEquals([1, 2], $this->s->evaluate('$a', $vars));
        $this->assertEquals(5, $this->s->evaluate('$b', $vars));
        $this->assertEquals(2, $this->s->evaluate('$c', $vars));

        $this->assertEquals(2.5, $this->s->evaluate('$ (b + c - 2) / 2', $vars));
    }
}
