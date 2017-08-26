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

use Itq\Common\Exception\MissingSudoPrivilegeException;
use Itq\Common\Tests\Exception\Base\AbstractExceptionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group exceptions
 * @group exceptions/missing-sudo-privilege
 */
class MissingSudoPrivilegeExceptionTest extends AbstractExceptionTestCase
{
    /**
     * @return MissingSudoPrivilegeException
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
        return [];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('Missing sudo privilege', $this->e()->getMessage());
        $this->assertEquals(403, $this->e()->getCode());
    }
}
