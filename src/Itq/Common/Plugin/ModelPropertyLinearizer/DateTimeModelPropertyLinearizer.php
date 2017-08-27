<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelPropertyLinearizer;

use Closure;
use DateTime;
use Exception;
use MongoDate;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DateTimeModelPropertyLinearizer extends Base\AbstractModelPropertyLinearizer
{
    /**
     * @param array  $data
     * @param string $k
     * @param mixed  $v
     * @param array  $meta
     * @param array  $options
     *
     * @return bool
     */
    public function supports(array &$data, $k, $v, array &$meta, array $options = [])
    {
        return (true === isset($meta['types'][$k]['type'])) && ('DateTime' === substr($meta['types'][$k]['type'], 0, 8));
    }
    /**
     * @param array   $data
     * @param string  $k
     * @param mixed   $v
     * @param array   $meta
     * @param Closure $objectLinearizer
     * @param array   $options
     *
     * @throws Exception
     */
    public function linearize(array &$data, $k, $v, array &$meta, Closure $objectLinearizer, array $options = [])
    {
        if (!isset($data[$k])) {
            throw $this->createRequiredException("Missing date time field '%s'", $k);
        }

        if (null !== $data[$k] && !$data[$k] instanceof DateTime) {
            throw $this->createRequiredException("Field '%s' must be a valid DateTime", $k);
        }

        /** @var DateTime $date */
        $date                       = $data[$k];
        $data[$k]                   = new MongoDate($date->getTimestamp());
        $data[sprintf('%s_tz', $k)] = $date->getTimezone()->getName();
    }
}
