<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Exception;
use RuntimeException;
use Itq\Common\Service\CriteriumService;
use Itq\Common\Plugin\CriteriumType\ValueCriteriumType;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/criterium
 */
class CriteriumServiceTest extends AbstractServiceTestCase
{
    /**
     * @return CriteriumService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testAddCriteriumTypes()
    {
        $this->assertEquals([], $this->s()->getSetCriteriumTypeNames('unknown_set'));
        $this->assertEquals([], $this->s()->getSetCriteriumTypes('unknown_set'));
    }
    /**
     * @param mixed  $expected
     * @param string $set
     * @param mixed  $criteria
     * @param array  $criteriumTypes
     *
     * @group unit
     *
     * @dataProvider getBuildSetQueryData
     */
    public function testBuildSetQuery($expected, $set, $criteria, array $criteriumTypes = [])
    {
        foreach ($criteriumTypes as $criteriumType) {
            $this->s()->addSetCriteriumType($criteriumType[0], $criteriumType[1], $criteriumType[2]);
        }

        if ($expected instanceof Exception) {
            $this->expectExceptionThrown($expected);
        }

        $result = $this->s()->buildSetQuery($set, $criteria);

        if (!($expected instanceof Exception)) {
            $this->assertEquals($expected, $result);
        }
    }
    /**
     * @return array
     */
    public function getBuildSetQueryData()
    {
        return [
            [
                [],
                'set1',
                'not an array',
            ],
            [
                new RuntimeException("No 'default' in set1 of setCriteriumTypes list", 412),
                'set1',
                ['a' => '*abc*'],
            ],
            [
                ['a' => 'this is the default'],
                'set1',
                ['a' => '*abc*'],
                [
                    ['set1', 'default', new ValueCriteriumType('this is the default')],
                ],
            ],
            [
                ['a' => 'this is the specific'],
                'set1',
                ['a' => '*abc*'],
                [
                    ['set1', 'default', new ValueCriteriumType('this is the default')],
                    ['set1', 'abc', new ValueCriteriumType('this is the specific')],
                ],
            ],
            [
                ['a' => 'zzbbzzbb', 'b' => '*cde*', 'd' => 'efg', 'e' => '*hij'],
                'set1',
                ['a' => '*abc*:aabbaabb', 'b' => '*cde*', 'd' => 'efg', 'e' => '*hij'],
                [
                    ['set1', 'default', new ValueCriteriumType(
                        function ($v) {
                            return [$v];
                        }
                    ),
                    ],
                    ['set1', 'abc', new ValueCriteriumType(
                        function ($v) {
                            return [strtr($v, 'a', 'z')];
                        }
                    ),
                    ],
                ],
            ],
            [
                ['b' => '*cde*', 'd' => 'efg', 'e' => '*hij', 'z' => 'aabbaabb'],
                'set1',
                ['a' => '*abc*:aabbaabb', 'b' => '*cde*', 'd' => 'efg', 'e' => '*hij'],
                [
                    ['set1', 'default', new ValueCriteriumType(
                        function ($v) {
                            return [$v];
                        }
                    ),
                    ],
                    ['set1', 'abc', new ValueCriteriumType(
                        function ($v) {
                            return [[], ['z' => $v]];
                        }
                    ),
                    ],
                ],
            ],
            [
                ['x' => ['a' => 'b']],
                'set1',
                ['x' => ['a' => 'b']],
                [
                    ['set1', 'default', new ValueCriteriumType(
                        function ($v) {
                            return [$v];
                        }
                    ),
                    ],
                ],
            ],
            [
                ['theotherkey' => ['a' => 'b']],
                'set1',
                ['x' => ['a' => 'b']],
                [
                    ['set1', 'default', new ValueCriteriumType(
                        function ($v) {
                            return [[], ['theotherkey' => $v]];
                        }
                    ),
                    ],
                ],
            ],
            [
                ['$or' => [['_id' => 'b'], ['a' => '*f1*']]],
                'set1',
                ['$or' => [['_id' => 'b'], ['a' => '*f1*']]],
                [
                    ['set1', 'default', new ValueCriteriumType(
                        function ($v) {
                            return [$v];
                        }
                    ),
                    ],
                ],
            ],
        ];
    }
}
