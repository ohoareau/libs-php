<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common;

use Itq\Common\Traits;
use Itq\Common\Tests\Base\AbstractBasicTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group misc
 */
class MiscTest extends AbstractBasicTestCase
{
    use Traits\ObjectTrait;
    /**
     * @group unit
     */
    public function testGetClass()
    {
        $this->assertEquals(MiscTest::class, $this->getClass());
    }
    /**
     * @group integ
     */
    public function testGetClassFile()
    {
        $this->assertEquals(__FILE__, $this->getClassFile());
    }
    /**
     * @group integ
     */
    public function testGetClassDirectory()
    {
        $this->assertEquals(__DIR__, $this->getClassDirectory());
    }
    /**
     * @group integ
     */
    public function testGetClassShortName()
    {
        $this->assertEquals('MiscTest', $this->getClassShortName());
    }
}
