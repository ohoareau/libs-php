<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common;

use Itq\Common\Bag;

use PHPUnit_Framework_TestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group bag
 */
class BagTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group unit
     */
    public function testGetVariables()
    {
        $b = new Bag(['g' => 'h']);

        $this->assertTrue($b->has('g'));
        $this->assertFalse($b->has('i'));

        $this->assertEquals('h', $b->get('g'));
    }
}
