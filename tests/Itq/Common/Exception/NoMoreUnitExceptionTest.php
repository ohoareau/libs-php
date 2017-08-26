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

use Itq\Common\Exception\NoMoreUnitException;
use Itq\Common\Tests\Exception\Base\AbstractExceptionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group exceptions
 * @group exceptions/no-more-unit
 */
class NoMoreUnitExceptionTest extends AbstractExceptionTestCase
{
    /**
     * @return NoMoreUnitException
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
        return ['the message', 405, null, ['a' => 45]];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('the message', $this->e()->getMessage());
        $this->assertEquals(405, $this->e()->getCode());
        $this->assertEquals(['a' => 45], $this->e()->getContext());
        $this->assertEquals(null, $this->e()->getPrevious());
    }
}
