<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Helper;

/**
 * Value trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ValueTrait
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isNotNull($value)
    {
        return null !== $value;
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isNull($value)
    {
        return null === $value;
    }
}
