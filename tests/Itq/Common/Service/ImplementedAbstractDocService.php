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

use Itq\Common\Service\Base\AbstractDocService;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ImplementedAbstractDocService extends AbstractDocService
{
    /**
     * @return int
     */
    public function getExpectedTypeCount()
    {
        return 0;
    }
    /**
     * @param array         $items
     * @param array         $criteria
     * @param array         $fields
     * @param \Closure|null $eachCallback
     * @param array         $options
     *
     * @return $this
     */
    public function filter(&$items, $criteria = [], $fields = [], \Closure $eachCallback = null, $options = [])
    {
        return $this->filterItems($items, $criteria, $fields, $eachCallback, $options);
    }
}
