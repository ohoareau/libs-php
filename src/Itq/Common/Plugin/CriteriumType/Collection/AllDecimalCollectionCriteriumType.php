<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CriteriumType\Collection;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AllDecimalCollectionCriteriumType extends Base\AbstractCollectionCriteriumType
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
        if ($this->isEmptyString($v)) {
            return [[], []];
        }

        return [
            [],
            [
                '+all_double' => array_map(
                    function ($vv) {
                        return (double) $vv;
                    },
                    explode(',', $v)
                ),
            ],
        ];
    }
}
