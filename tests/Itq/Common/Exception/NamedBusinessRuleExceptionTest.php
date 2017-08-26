<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Exception;

use RuntimeException;
use Itq\Common\Exception\NamedBusinessRuleException;
use Itq\Common\Tests\Exception\Base\AbstractExceptionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group exceptions
 * @group exceptions/named-business-rule
 */
class NamedBusinessRuleExceptionTest extends AbstractExceptionTestCase
{
    /**
     * @return NamedBusinessRuleException
     */
    public function e()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::e();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return ['br1', 'theName', ['a' => 23], new RuntimeException('the previous exception', 402)];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals("Business rule #br1 'theName' error: the previous exception", $this->e()->getMessage());
        $this->assertEquals(402, $this->e()->getCode());
        $this->assertEquals('br1', $this->e()->getId());
        $this->assertEquals('theName', $this->e()->getName());
        $this->assertEquals(['a' => 23], $this->e()->getData());
        $this->assertEquals(null, $this->e()->getPrevious());
        $this->assertEquals('the previous exception', $this->e()->getBusinessRuleException()->getMessage());
        $this->assertEquals(402, $this->e()->getBusinessRuleException()->getCode());
    }
}
