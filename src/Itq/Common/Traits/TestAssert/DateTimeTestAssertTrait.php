<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\TestAssert;

use DateTime;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DateTimeTestAssertTrait
{
    /**
     * @param DateTime $expected
     * @param DateTime $actual
     */
    public static function assertDateTimeEquals(DateTime $expected, DateTime $actual)
    {
        static::assertEquals($expected->format('c'), $actual->format('c'));
    }
}
