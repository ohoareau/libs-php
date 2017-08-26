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

use Itq\Common\Exception\BulkException;
use Itq\Common\Tests\Exception\Base\AbstractExceptionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group exceptions
 * @group exceptions/bulk
 */
class BulkExceptionTest extends AbstractExceptionTestCase
{
    /**
     * @return BulkException
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
        return [[], [], []];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('Bulk exception', $this->e()->getMessage());
        $this->assertEquals(412, $this->e()->getCode());
        $this->assertEquals([], $this->e()->getExceptions());
        $this->assertEquals(0, $this->e()->getExceptionCount());
        $this->assertEquals([], $this->e()->getErrorData());
        $this->assertEquals(0, $this->e()->getErrorCount());
        $this->assertEquals([], $this->e()->getSuccessData());
        $this->assertEquals(0, $this->e()->getSuccessCount());
    }
}
