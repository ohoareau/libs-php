<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CriteriumType\Mongo;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class WithinCircleMongoCriteriumType extends Base\AbstractMongoCriteriumType
{
    /**
     * @param string $v
     * @param string $k
     * @param array  $options
     *
     * @return array
     */
    public function build($v, $k, array $options = [])
    {
        $_tokens = explode(' ', $v);
        $lng     = 0.0;
        $lat     = 0.0;
        $radius  = 10000; // 10 kms

        if (isset($_tokens)) {
            $lng = (float) array_shift($_tokens);
        }
        if (isset($_tokens)) {
            $lat = (float) array_shift($_tokens);
        }
        if (isset($_tokens)) {
            $radius = (float) array_shift($_tokens);
        }

        return [
            [
                '$geoWithin' => [
                    '$centerSphere' => [
                        [$lng, $lat],
                        ($radius / 1000) / 6378.1,
                    ],
                ],
            ],
        ];
    }
}
