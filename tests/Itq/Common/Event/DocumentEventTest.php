<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Event;

use Itq\Common\Event\DocumentEvent;
use Itq\Common\Tests\Event\Base\AbstractEventTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group events
 * @group events/document
 */
class DocumentEventTest extends AbstractEventTestCase
{
    /**
     * @return DocumentEvent
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
        return [[], ['a' => 1, 'b' => 10, 'c' => null]];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals([], $this->e()->getData());
        $this->assertEquals(['a' => 1, 'b' => 10, 'c' => null], $this->e()->getContext());
        $this->assertEquals(1, $this->e()->getContextVariable('a'));
    }
    /**
     * @param mixed  $expected
     * @param string $key
     * @param mixed  $default
     *
     * @group unit
     *
     * @dataProvider getContextVariableData
     */
    public function testContextVariable($expected, $key, $default = null)
    {
        $this->assertEquals($expected, $this->e()->getContextVariable($key, $default));
    }
    /**
     * @return array
     */
    public function getContextVariableData()
    {
        return [
            [1, 'a'],
            [10, 'b'],
            [null, 'c'],
            [100, 'c', 100],
            [null, 'd'],
            [1000, 'd', 1000],
        ];
    }
}
