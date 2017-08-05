<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * Filter Service.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
class FilterService
{
    use Traits\ServiceTrait;
    use Traits\FilterItemsTrait;
    /**
     * @param array    $items
     * @param array    $criteria
     * @param array    $fields
     * @param \Closure $eachCallback
     * @param array    $options
     *
     * @return $this
     */
    public function filter(&$items, $criteria = [], $fields = [], \Closure $eachCallback = null, $options = [])
    {
        return $this->filterItems($items, $criteria, $fields, $eachCallback, $options);
    }
}
