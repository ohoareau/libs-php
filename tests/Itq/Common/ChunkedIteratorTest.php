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

use Itq\Common\ChunkedIterator;
use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group iterators
 * @group iterators/chunked
 */
class ChunkedIteratorTest extends AbstractTestCase
{
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            function () {
                return [];
            },
            5,
        ];
    }
    /**
     * @param array         $loops
     * @param int           $chunkSize
     * @param int           $limit
     * @param array         $expectedLoopLimits
     * @param array         $expectedResults
     * @param int           $expectedLoopCount
     * @param \Closure|null $itemCallback
     *
     * @group unit
     * @dataProvider getForeachData
     */
    public function testForeach($loops, $chunkSize, $limit, $expectedLoopLimits, $expectedResults, $expectedLoopCount, \Closure $itemCallback = null)
    {
        $loopLimits = [];

        $it = new ChunkedIterator(
            function ($loopLimit) use ($loops, &$loopLimits) {
                static $n = 0;
                $v = isset($loops[$n]) ? $loops[$n] : [];
                $loopLimits[$n] = $loopLimit;
                $n++;

                return $v;
            },
            $chunkSize,
            $limit,
            $itemCallback
        );

        $actual = [];

        foreach ($it as $k => $results) {
            $actual = array_merge($actual, $results);
        }

        $this->assertEquals($expectedLoopLimits, $loopLimits, "Loop limits not same");
        $this->assertEquals($expectedLoopCount, $it->getCurrentLoopCount());
        $this->assertEquals($expectedResults, $actual);
    }
    /**
     * @return array
     */
    public function getForeachData()
    {
        return [
            [[[1, 2, 3, 4, 5]], 10, null, [10], [1, 2, 3, 4, 5], 1],
            [[[1, 2, 3, 4], [5]], 4, null, [4, 4], [1, 2, 3, 4, 5], 2],
            [[[1, 2, 3, 4]], 4, null, [4, 4], [1, 2, 3, 4], 2],
            [[[1, 2], [3, 4], [5, 6]], 2, null, [2, 2, 2, 2], [1, 2, 3, 4, 5, 6], 4],
            [[[1, 2, 3, 4, 5]], 10, 5, [5], [1, 2, 3, 4, 5], 1],
            [[[1, 2, 3, 4, 5]], 5, 5, [5], [1, 2, 3, 4, 5], 1],
            [[[1, 2, 3, 4], [5, 6]], 4, 6, [4, 2], [1, 2, 3, 4, 5, 6], 2],
            [[new \ArrayObject([1, 2, 3, 4]), new \ArrayObject([5, 6])], 4, 6, [4, 2], [1, 2, 3, 4, 5, 6], 2],
            [
                [[1, 2, 3, 4, 5]],
                10,
                null,
                [10],
                [2, 4, 6, 8, 10],
                1,
                function ($v) {
                    return 2*$v;
                },
            ],
        ];
    }
}
