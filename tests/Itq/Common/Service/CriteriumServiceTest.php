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
use Itq\Common\Plugin\CriteriumType;
use Itq\Common\Service\CriteriumService;
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
                    ['set1', 'default', new CriteriumType\ValueCriteriumType('this is the default')],
                ],
            ],
            [
                ['a' => 'this is the specific'],
                'set1',
                ['a' => '*abc*'],
                [
                    ['set1', 'default', new CriteriumType\ValueCriteriumType('this is the default')],
                    ['set1', 'abc', new CriteriumType\ValueCriteriumType('this is the specific')],
                ],
            ],
            [
                ['a' => 'zzbbzzbb', 'b' => '*cde*', 'd' => 'efg', 'e' => '*hij'],
                'set1',
                ['a' => '*abc*:aabbaabb', 'b' => '*cde*', 'd' => 'efg', 'e' => '*hij'],
                [
                    ['set1', 'default', new CriteriumType\ValueCriteriumType(
                        function ($v) {
                            return [$v];
                        }
                    ),
                    ],
                    ['set1', 'abc', new CriteriumType\ValueCriteriumType(
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
                    ['set1', 'default', new CriteriumType\ValueCriteriumType(
                        function ($v) {
                            return [$v];
                        }
                    ),
                    ],
                    ['set1', 'abc', new CriteriumType\ValueCriteriumType(
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
                    ['set1', 'default', new CriteriumType\ValueCriteriumType(
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
                    ['set1', 'default', new CriteriumType\ValueCriteriumType(
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
                    ['set1', 'default', new CriteriumType\ValueCriteriumType(
                        function ($v) {
                            return [$v];
                        }
                    ),
                    ],
                ],
            ],
            [
                ['not_exists' => ['a' => true, 'c' => false], 'exists' => ['b' => false, 'd' => true], 'qqq' => 12],
                'set1',
                ['a' => '*xxx*', 'b' => '*yyy*', 'c' => '*zzz*', 'd' => '*ttt*', 'e' => '*uuu*:11', 'f' => '*uuu*:12'],
                [
                    ['set1', 'xxx', new CriteriumType\ValueCriteriumType(
                        function ($v, $k) {
                            unset($v);

                            return [[], ['+not_exists' => [$k => true]]];
                        }
                    ),
                    ],
                    ['set1', 'yyy', new CriteriumType\ValueCriteriumType(
                        function ($v, $k) {
                            unset($v);

                            return [[], ['+exists' => [$k => false]]];
                        }
                    ),
                    ],
                    ['set1', 'zzz', new CriteriumType\ValueCriteriumType(
                        function ($v, $k) {
                            unset($v);

                            return [[], ['+not_exists' => [$k => false]]];
                        }
                    ),
                    ],
                    ['set1', 'ttt', new CriteriumType\ValueCriteriumType(
                        function ($v, $k) {
                            unset($v);

                            return [[], ['+exists' => [$k => true]]];
                        }
                    ),
                    ],
                    ['set1', 'uuu', new CriteriumType\ValueCriteriumType(
                        function ($v) {
                            return [[], ['qqq' => (int) $v]];
                        }
                    ),
                    ],
                ],
            ],
        ];
    }
    /**
     * @param mixed  $expected
     * @param string $set
     * @param mixed  $criteria
     * @param array  $criteriumTypes
     *
     * @group integ
     *
     * @dataProvider getBuildSetQueryForRealCriteriumTypesData
     */
    public function testBuildSetQueryForRealCriteriumTypes($expected, $set, $criteria, array $criteriumTypes = [])
    {
        foreach ($criteriumTypes as $setName => $setCriteriumTypes) {
            foreach ($setCriteriumTypes as $criteriumType) {
                $this->s()->addSetCriteriumType($setName, $criteriumType[0], $criteriumType[1]);
            }
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
    public function getBuildSetQueryForRealCriteriumTypesData()
    {
        return [
            [
                [
                    'exists'        => ['a' => true, 'b' => false],
                    'equals_bool'   => ['c' => true, 'd' => false, 'k' => false, 'l' => true, 'm' => false],
                    'different'     => ['e' => 'abc', 'f' => 'efg', 'g' => 'abc', 'h' => 'efg'],
                    'different_int' => ['i' => 12, 'j' => 12],
                ],
                'collection',
                [
                    'a' => '*notempty*', 'b' => '*empty*',
                    'c' => '*true*', 'd' => '*false*',
                    'e' => '*not*:abc', 'f' => '*not*:efg',
                    'g' => '*ne*:abc', 'h' => '*ne*:efg',
                    'i' => '*not_int*:12', 'j' => '*not_int*:12.5',
                    'k' => '*not_bool*:1', 'l' => '*not_bool*:0', 'm' => '*not_bool*:a',
                ],
                [
                    'collection' => [
                        ['notempty', new CriteriumType\Collection\NotEmptyCollectionCriteriumType()],
                        ['empty', new CriteriumType\Collection\EmptyCollectionCriteriumType()],
                        ['false', new CriteriumType\Collection\FalseCollectionCriteriumType()],
                        ['true', new CriteriumType\Collection\TrueCollectionCriteriumType()],
                        ['not', new CriteriumType\Collection\NotCollectionCriteriumType()],
                        ['ne', new CriteriumType\Collection\NotCollectionCriteriumType()],
                        ['not_int', new CriteriumType\Collection\NotEqualIntegerCollectionCriteriumType()],
                        ['not_bool', new CriteriumType\Collection\NotEqualBoolCollectionCriteriumType()],
                        ['not_dec', new CriteriumType\Collection\NotEqualDecimalCollectionCriteriumType()],
                    ],
                ],
            ],
        ];
    }
}
