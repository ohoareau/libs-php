<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CriteriumType\Mongo\Base;

use Itq\Common\Plugin\CriteriumType\Base\AbstractCriteriumType;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractMongoCriteriumType extends AbstractCriteriumType
{
    /**
     * @param string|array $k
     * @param mixed  $v
     *
     * @return mixed
     */
    protected function prepare($k, $v)
    {
        if ('_id' === $k || 'id' === $k) {
            return $this->ensureId($v);
        }

        return $v;
    }
}
