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

use Itq\Common\Exception\UnsupportedTokenGeneratorTypeException;
use Itq\Common\Tests\Exception\Base\AbstractExceptionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group exceptions
 * @group exceptions/unsupported-token-generator-type
 */
class UnsupportedTokenGeneratorTypeExceptionTest extends AbstractExceptionTestCase
{
    /**
     * @return UnsupportedTokenGeneratorTypeException
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
        return ['thetype'];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals("Unsupported token generator type 'thetype'", $this->e()->getMessage());
        $this->assertEquals(403, $this->e()->getCode());
    }
}
