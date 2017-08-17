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

use Itq\Common\Bag;
use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group objects
 * @group objects/bag
 */
class BagTest extends AbstractTestCase
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
