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

use Itq\Common\Service;

use PHPUnit_Framework_TestCase;
use RuntimeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group math
 */
class MathServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Service\MathService
     */
    protected $s;
    /**
     *
     */
    public function setUp()
    {
        $this->s = new Service\MathService();
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->s);
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
            $this->s->deduplicate($points, 'sum')
        );
    }
    /**
     * @group unit
     */
    public function testDeduplicateMedianMode()
    {
        $points = [
            ['2016-06-27' => 10],
            ['2016-06-27' => 15],
            ['2016-06-27' => 20],
            ['2016-06-28' => 5],
        ];

        $this->assertEquals(
            [
                '2016-06-27' => 15,
                '2016-06-28' => 5,
            ],
            $this->s->deduplicate($points, 'median')
        );
    }
    /**
     * @group unit
     */
    public function testDeduplicateMinMode()
    {
        $points = [
            ['2016-06-27' => 10],
            ['2016-06-27' => 20],
            ['2016-06-27' => 10],
            ['2016-06-28' => 5],
        ];

        $this->assertEquals(
            [
                '2016-06-27' => 10,
                '2016-06-28' => 5,
            ],
            $this->s->deduplicate($points, 'min')
        );
    }
    /**
     * @group unit
     */
    public function testDeduplicateMaxMode()
    {
        $points = [
            ['2016-06-27' => 10],
            ['2016-06-27' => 20],
            ['2016-06-27' => 10],
            ['2016-06-28' => 5],
        ];

        $this->assertEquals(
            [
                '2016-06-27' => 20,
                '2016-06-28' => 5,
            ],
            $this->s->deduplicate($points, 'max')
        );
    }

    /**
     *
     */
    public function testDeduplicateDefaultMode()
    {
        $points = [
            ['2016-06-27' => 10],
            ['2016-06-27' => 20],
            ['2016-06-27' => 10],
            ['2016-06-28' => 5],
        ];
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage("Unsupported deduplication mode 'medium'");
        $this->s->deduplicate($points, 'medium');
    }
    public function testStats1()
    {
        $result = $this->s->stats([0, 1, 2, 3, 4, 5]);
        $this->assertEquals([
            'min' => 0,
            'max' => 5,
            'count' => 6,
            'sum' => 15,
            'median' => 2.5,
            'average' => 2.5,
            'percentile90' => 4.5
        ], $result);
    }
    public function testStats2()
    {
        $result = $this->s->stats([2]);
        $this->assertEquals([
            'min' => 2,
            'max' => 2,
            'count' => 1,
            'sum' => 2,
            'median' => 2,
            'average' => 2,
            'percentile90' => 2
        ], $result);
    }
    public function testStats3()
    {
        $result = $this->s->stats([3, 0, 1.5, 31, -6]);
        $this->assertEquals([
            'min' => -6,
            'max' => 31,
            'count' => 5,
            'sum' => 29.5,
            'median' => 1.5,
            'average' => 5.9,
            'percentile90' => 19.8
        ], $result);
    }
    // test with 1 < $rank && $rank <= 100
    public function testPercentile1()
    {
        $result = $this->s->percentile(50, [1, 10, 50, 100]);
        $this->assertEquals(30, $result);
    }
    // test of 0 == count($population) and 1 < $rank <= 100
    public function testPercentile2()
    {
        $result = $this->s->percentile(50, []);
        $this->assertEquals(0, $result);
    }
    // test of false === is_float($floatval)
   /** public function testPercentile4()
    {
        $result = $this->s->percentile(1/3, [1, 2]);

        $this->assertEquals(1, $result);
    }
    * */
    /**public function testShuffle()
    {
        $t = [1, 2, 3];
        shuffle($t);
        $this->assertEquals([2], $t);
    }*/
    public function testRandForPositiveIntegerIntervalReturnAnIntegerInTheInterval()
    {
        $result = $this->s->rand(1, 5);
        $this->assertTrue(1 <= $result && $result <= 5);
    }
}
