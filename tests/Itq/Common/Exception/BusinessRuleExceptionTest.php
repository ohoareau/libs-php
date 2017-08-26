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
use Itq\Common\Exception\BusinessRuleException;
use Itq\Common\Tests\Exception\Base\AbstractExceptionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group exceptions
 * @group exceptions/business-rule
 */
class BusinessRuleExceptionTest extends AbstractExceptionTestCase
{
    /**
     * @return BusinessRuleException
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
        return ['the message', 412, 'subType', ['a' => 1], new RuntimeException('the previous exception', 413)];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('the message', $this->e()->getMessage());
        $this->assertEquals(412, $this->e()->getCode());
        $this->assertEquals('subType', $this->e()->getSubType());
        $this->assertEquals(['a' => 1], $this->e()->getData());
        $this->assertEquals('the previous exception', $this->e()->getPrevious()->getMessage());
        $this->assertEquals(413, $this->e()->getPrevious()->getCode());
    }
}
