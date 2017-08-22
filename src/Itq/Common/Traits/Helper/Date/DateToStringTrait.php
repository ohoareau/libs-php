<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Helper\Date;

use DateTime;

/**
 * Date To String trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DateToStringTrait
{
    /**
     * @param DateTime $date
     * @param string   $format
     *
     * @return string
     */
    protected function convertDateToString(DateTime $date, $format = DateTime::ISO8601)
    {
        return $date->format($format);
    }
}
