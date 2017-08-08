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
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/expression
 */
class ExpressionServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\ExpressionService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mockedTemplateService(),
            $this->mockedExpressionLanguage(),
        ];
    }
    /**
     * @group integ
     */
    public function testEvaluateExpressionLanguage()
    {
        $this->s()->setExpressionLanguage(new ExpressionLanguage());

        $vars = ['a' => [1, 2], 'b' => 5, 'c' => 2];

        $this->assertEquals([1, 2], $this->s()->evaluate('$a', $vars));
        $this->assertEquals(5, $this->s()->evaluate('$b', $vars));
        $this->assertEquals(2, $this->s()->evaluate('$c', $vars));

        $this->assertEquals(2.5, $this->s()->evaluate('$ (b + c - 2) / 2', $vars));
    }
}
