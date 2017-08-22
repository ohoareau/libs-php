<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use DateTime;
use Exception;
use DateInterval;
use Itq\Common\Traits;

/**
 * Date Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DateService
{
    use Traits\ServiceTrait;
    use Traits\Helper\Date\DateToStringTrait;
    use Traits\Helper\Date\StringToDateTrait;
    /**
     * @param DateTime $now
     * @param int      $minDays
     * @param int      $maxDays
     * @param bool     $businessDaysOnly
     *
     * @return array
     */
    public function computeIntervalInDays(\DateTime $now, $minDays, $maxDays, $businessDaysOnly = false)
    {
        return [
            $this->computeDateInFuture($now, $minDays, $businessDaysOnly),
            $this->computeDateInFuture($now, $maxDays, $businessDaysOnly),
        ];
    }
    /**
     * @param DateTime $now
     * @param int      $days
     * @param bool     $businessDaysOnly
     *
     * @return DateTime
     */
    public function computeDateInFuture(\DateTime $now, $days, $businessDaysOnly = false)
    {
        $date = (clone $now);

        if (true !== $businessDaysOnly) {
            return $date->add(new DateInterval(sprintf('P%dD', $days)));
        }

        for ($i = 0; $i < $days; $i++) {
            switch ((int) $date->format('N')) {
                case 5:
                    // friday
                case 6:
                    // saturday
                    $offset = 3;
                    break;
                case 7:
                    // sunday
                    $offset = 2;
                    break;
                case 1:
                    // monday
                case 2:
                    // tuesday
                case 3:
                    // wednesday
                case 4:
                    // thursday
                default:
                    $offset = 1;
                    break;
            }
            $date->add(new DateInterval(sprintf('P%dD', $offset)));
        }

        return $date;
    }
    /**
     * @param DateTime $date
     * @param array    $holidays
     *
     * @return DateTime
     */
    public function shiftDateOutsideHolidays(\DateTime $date, $holidays)
    {
        foreach ($holidays as $holiday) {
            $start = new DateTime($holiday[0]);
            $end = new DateTime($holiday[1]);
            if ($date >= $start && $date < $end) {
                $date = $end;
            }
        }

        return $date;
    }
    /**
     * @param DateTime $date
     * @param string   $periodUnit
     *
     * @return string
     *
     * @throws Exception
     */
    public function getPeriodLabel(DateTime $date, $periodUnit)
    {
        switch ($periodUnit) {
            case 'year':
                $value = $date->format('Y');
                break;
            case 'half':
                $value  = $date->format('Y').'-';
                $value .= (((int) $date->format('m') > 6) ? 'S2' : 'S1');
                break;
            case 'quarter':
                $value = $date->format('Y').'-';
                $m = (int) $date->format('m');
                if ($m >= 10) {
                    $value .= 'Q4';
                } elseif ($m >= 7) {
                    $value .= 'Q3';
                } elseif ($m >= 4) {
                    $value .= 'Q2';
                } else {
                    $value .= 'Q1';
                }
                break;
            case 'month':
                $value = $date->format('Y').'-'.(string) $date->format('m');
                break;
            case 'week':
                $value = $date->format('Y').'-'.'W'.(int) $date->format('W');
                break;
            case 'day':
                $value = $date->format('Y').'-'.$date->format('m').'-'.$date->format('d');
                break;
            case 'hour':
                $value = $date->format('Y').'-'.$date->format('m').'-'.$date->format('d').'_'.$date->format('H');
                break;
            case 'minute':
                $value = $date->format('Y').'-'.$date->format('m').'-'.$date->format('d').'_'.$date->format('H').'-'.$date->format('i');
                break;
            case 'second':
                $value = $date->format('Y').'-'.$date->format('m').'-'.$date->format('d').'_'.$date->format('H').'-'.$date->format('i').'-'.$date->format('s');
                break;
            default:
                throw $this->createFailedException("Unsupported period unit '%s'", $periodUnit);
        }

        return $value;
    }
    /**
     * @param string $date
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public function convertStringToDateTime($date)
    {
        return $this->convertStringToDate($date);
    }
    /**
     * @param DateTime $date
     *
     * @return string
     */
    public function convertDateTimeToString(DateTime $date)
    {
        return $this->convertDateToString($date);
    }
    /**
     * @param DateTime $date
     * @param DateTime $expirationDate
     *
     * @return bool
     */
    public function isDateExpired(DateTime $date, DateTime $expirationDate)
    {
        return $date > $expirationDate;
    }
    /**
     * @param DateTime $expirationDate
     *
     * @return bool
     */
    public function isDateExpiredFromNow(DateTime $expirationDate)
    {
        return $this->isDateExpired(new DateTime(), $expirationDate);
    }
}
