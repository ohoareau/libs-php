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

use Itq\Common\Exception\ErrorException;
use Itq\Common\Tests\Exception\Base\AbstractExceptionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group exceptions
 * @group exceptions/error
 */
class ErrorExceptionTest extends AbstractExceptionTestCase
{
    /**
     * @return ErrorException
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
        return ['message', 403, 'application.key', [], 10001, []];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('message', $this->e()->getMessage());
        $this->assertEquals(403, $this->e()->getCode());
        $this->assertEquals('application.key', $this->e()->getApplicationKey());
        $this->assertEquals(10001, $this->e()->getApplicationCode());
        $this->assertEquals([], $this->e()->getApplicationMetaData());
        $this->assertEquals([], $this->e()->getApplicationParams());
    }
}
