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

use Itq\Common\Service\MathService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

use RuntimeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group  services/math
 * @group  services
 */
class MathServiceTest extends AbstractServiceTestCase
{
    /**
     * @return MathService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testDeduplicateSumMode()
    {
        $points = [
            ['2016-06-27' => 10],
            ['2016-06-27' => 20],
            ['2016-06-27' => 10],
            ['2016-06-28' => 5],
        ];

        $this->assertEquals(
            [
                '2016-06-27' => 40,
                '2016-06-28' => 5,
            ],
            $this->s()->deduplicate($points, 'sum')
        );
    }
    /**
     * @group unit
     */
    public function testDeduplicateMedianMode()
    {
        $this->assertEquals(
            ['2016-06-27' => 15, '2016-06-28' => 5],
            $this->s()->deduplicate(
                [['2016-06-27' => 10], ['2016-06-27' => 15], ['2016-06-27' => 20], ['2016-06-28' => 5]],
                'median'
            )
        );
    }
    /**
     * @group unit
     */
    public function testDeduplicateMinMode()
    {
        $this->assertEquals(
            ['2016-06-27' => 10, '2016-06-28' => 5],
            $this->s()->deduplicate(
                [['2016-06-27' => 10], ['2016-06-27' => 20], ['2016-06-27' => 10], ['2016-06-28' => 5]],
                'min'
            )
        );
    }
    /**
     * @group unit
     */
    public function testDeduplicateMaxMode()
    {
        $this->assertEquals(
            ['2016-06-27' => 20, '2016-06-28' => 5],
            $this->s()->deduplicate(
                [['2016-06-27' => 10], ['2016-06-27' => 20], ['2016-06-27' => 10], ['2016-06-28' => 5]],
                'max'
            )
        );
    }
    /**
     * @group unit
     */
    public function testDeduplicateDefaultMode()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage("Unsupported deduplication mode 'medium'");

        $this->s()->deduplicate(
            [['2016-06-27' => 10], ['2016-06-27' => 20], ['2016-06-27' => 10], ['2016-06-28' => 5]],
            'medium'
        );
    }
    /**
     * @group unit
     */
    public function testStats1()
    {
        $this->assertEquals(
            [
                'min' => 0,
                'max' => 5,
                'count' => 6,
                'sum' => 15,
                'median' => 2.5,
                'average' => 2.5,
                'percentile90' => 4.5,
            ],
            $this->s()->stats([0, 1, 2, 3, 4, 5])
        );
    }
    /**
     * @group unit
     */
    public function testStats2()
    {
        $this->assertEquals(
            ['min' => 2, 'max' => 2, 'count' => 1, 'sum' => 2, 'median' => 2, 'average' => 2, 'percentile90' => 2],
            $this->s()->stats([2])
        );
    }
    /**
     * @group unit
     */
    public function testStats3()
    {
        $this->assertEquals(
            [
                'min' => -6,
                'max' => 31,
                'count' => 5,
                'sum' => 29.5,
                'median' => 1.5,
                'average' => 5.9,
                'percentile90' => 19.8,
            ],
            $this->s()->stats([3, 0, 1.5, 31, -6])
        );
    }
    /**
     * @param int               $rank
     * @param array             $population
     * @param string            $field
     * @param \RuntimeException $exception
     *
     * @group unit
     *
     * @dataProvider getPercentileException
     */
    public function testPercentileThrowException($rank, $population, $field, $exception)
    {

        $this->expectExceptionThrown($exception);
        $this->s()->percentile($rank, $population, $field);
    }
    /**
     * @return array
     */
    public function getPercentileException()
    {
        return [
            '0 - malformatted rank' => [
                115,
                [],
                null,
                new \RuntimeException('Percentile must be 0 < p < 1 or 1 < p <= 100 (found: 115.000000)', 412),
            ],
            '1 - missing field' => [
                50,
                [['value' => 1], ['value2' => 10]],
                'value',
                new \RuntimeException('Field \'value\' is not available in population', 412),
            ],
        ];
    }
    /**
     * @param int    $rank
     * @param array  $population
     * @param string $field
     * @param int    $expected
     *
     * @group unit
     *
     * @dataProvider getPercentileData
     */
    public function testPercentile($rank, $population, $field, $expected)
    {
        $this->assertEquals($expected, $this->s()->percentile($rank, $population, $field));
    }
    /**
     * @return array
     */
    public function getPercentileData()
    {
        return [
            '0 - 0 < rank < 1' => [0.5, [1, 10, 50, 100], null, 30],
            '1 - 1 < rank <= 100' => [50, [1, 10, 50, 100], null, 30],
            '2 - 1 < rank <= 100 no population' => [50, [], null, 0],
            '3 - with field' => [
                50,
                [['value' => 1], ['value' => 10], ['value' => 50], ['value' => 100]],
                'value',
                30,
            ],
        ];
    }
    /**
     * @group unit
     */
    public function testRandForPositiveIntegerIntervalReturnAnIntegerInTheInterval()
    {
        $result = $this->s()->rand(1, 5);
        $this->assertTrue(1 <= $result && $result <= 5);
    }
    /**
     * @group unit
     */
    public function testShuffle()
    {
        $data = [
            'position1',
            'postions2',
            'position3',
            'position4',
            'position5',
            'position6',
            'postions7',
            'postions8',
            'position9',
            'position10',
            'position11',
            'position12',
            'position13',
        ];
        $actual = $data;
        $this->s()->shuffle($actual);
        $diff = array_diff_assoc($actual, $data);
        if (true === empty($diff)) {
            // can be shuffle as same, so do it again
            $this->s()->shuffle($actual);
        }
        $this->assertNotEquals($data, $actual);
    }
}
