<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Exception;
use Itq\Common\Traits;

/**
 * Math Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MathService
{
    use Traits\ServiceTrait;
    /**
     * Return computed stats for the specified population.
     *
     * @param array $values
     *
     * @return array
     *
     * @throws Exception
     */
    public function stats($values)
    {
        return [
            'min'          => $this->min($values),
            'max'          => $this->max($values),
            'count'        => $this->count($values),
            'sum'          => $this->sum($values),
            'median'       => $this->median($values),
            'average'      => $this->average($values),
            'percentile90' => $this->percentile(0.9, $values),
        ];
    }
    /**
     * @param array  $points
     * @param string $mode
     *
     * @return array
     *
     * @throws Exception
     */
    public function deduplicate($points, $mode = 'sum')
    {
        $dedup = [];

        foreach ($points as $i => $point) {
            foreach ($point as $k => $v) {
                if (!isset($dedup[$k])) {
                    $dedup[$k] = [];
                }
                $dedup[$k][] = $v;
            }
            unset($points[$i]);
        }

        foreach ($dedup as $k => $v) {
            switch ($mode) {
                case 'sum':
                    $dedup[$k] = $this->sum($v);
                    break;
                case 'max':
                    $dedup[$k] = $this->max($v);
                    break;
                case 'min':
                    $dedup[$k] = $this->min($v);
                    break;
                case 'median':
                    $dedup[$k] = $this->median($v);
                    break;
                default:
                    throw $this->createFailedException("Unsupported deduplication mode '%s'", $mode);
            }
        }

        return $dedup;
    }
    /**
     * Return sum for the specified population.
     *
     * @param array $values
     *
     * @return number
     */
    public function sum($values)
    {
        return array_sum($values);
    }
    /**
     * Return min for the specified population.
     *
     * @param array $values
     *
     * @return number
     */
    public function min($values)
    {
        return min($values);
    }
    /**
     * Return max for the specified population.
     *
     * @param array $values
     *
     * @return number
     */
    public function max($values)
    {
        return max($values);
    }
    /**
     * Return count for the specified population.
     *
     * @param array $values
     *
     * @return int
     */
    public function count($values)
    {
        return count($values);
    }
    /**
     * Return median for the specified population.
     *
     * @param array $values
     *
     * @return number
     *
     * @throws Exception
     */
    public function median($values)
    {
        return $this->percentile(0.5, $values);
    }
    /**
     * Return average for the specified population.
     *
     * @param array $values
     *
     * @return float
     */
    public function average($values)
    {
        return $this->sum($values) / $this->count($values);
    }
    /**
     * Return specified percentile for the specified population.
     *
     * @param float       $rank
     * @param array       $population
     * @param null|string $field
     *
     * @return number
     *
     * @throws \Exception
     */
    public function percentile($rank, $population, $field = null)
    {
        if (0 < $rank && $rank < 1) {
            $p = $rank;
        } elseif (1 < $rank && $rank <= 100) {
            $p = $rank * .01;
        } else {
            throw $this->createMalformedException(
                'Percentile must be 0 < p < 1 or 1 < p <= 100 (found: %f)',
                $rank
            );
        }

        if (0 === count($population)) {
            return 0;
        }

        if (null === $field) {
            $data = $population;
        } else {
            $data = [];
            foreach ($population as $item) {
                if (false === isset($item[$field])) {
                    throw $this->createRequiredException(
                        "Field '%s' is not available in population",
                        $field
                    );
                }
                $data[] = $item[$field];
            }
        }
        $count = count($data);
        $allindex = ($count - 1) * $p;
        $intvalindex = intval($allindex);
        $floatval = $allindex - $intvalindex;
        sort($data);

        if ($count > $intvalindex + 1) {
            $result = $floatval
                * ($data[$intvalindex + 1] - $data[$intvalindex])
                + $data[$intvalindex];
        } else {
            $result = $data[$intvalindex];
        }


        return $result;
    }

    /**
     * Fisher–Yates shuffle using mt_rand.
     *
     * To shuffle an array a of n elements (indices 0..n-1):
     * for i from 0 to n − 2 do
     * j ← random integer such that i ≤ j < n
     * exchange a[j] and a[i]
     *
     * @param array $a
     */
    public function shuffle(array &$a)
    {
        mt_srand();

        for ($n = count($a), $i = 0; $i < ($n - 2); $i++) {
            $j     = $this->rand($i, $n - 1);
            $t     = $a[$j];
            $a[$j] = $a[$i];
            $a[$i] = $t;
        }

        $a = array_values($a);
    }
    /**
     * @param mixed $min
     * @param mixed $max
     *
     * @return int
     */
    public function rand($min, $max)
    {
        return mt_rand($min, $max);
    }
}
