<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

use DateTime;
use Exception;

/**
 * Notification Mode Provider Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface DateProviderInterface
{
    /**
     * @return DateTime
     */
    public function getCurrentDate();
    /**
     * @param DateTime $current
     *
     * @return $this
     */
    public function setCurrentDate(DateTime $current);
    /**
     * @param string $current
     *
     * @return $this
     */
    public function setCurrentDateFromString($current);
    /**
     * @return $this
     */
    public function resetCurrentDateToSystemDate();
    /**
     * @param string $date
     *
     * @return $this
     *
     * @throws Exception
     */
    public function checkDateStringFormat($date);
}
