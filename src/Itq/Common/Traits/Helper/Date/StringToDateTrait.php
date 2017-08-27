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
use Exception;

/**
 * String To Date trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait StringToDateTrait
{
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    abstract protected function createMalformedException($msg, ...$params);
    /**
     * @param string $date
     *
     * @return DateTime
     *
     * @throws Exception
     */
    protected function convertStringToDate($date)
    {
        $value = DateTime::createFromFormat(DateTime::ISO8601, $date);

        if (false === $value) {
            throw $this->createMalformedException("Expiration date malformed: %s", (string) $date);
        }

        return $value;
    }
}
