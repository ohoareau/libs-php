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

use Itq\Common\Traits;

/**
 * Shipping Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ShippingService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\DateServiceAwareTrait;
    /**
     * @param DateService $dateService
     */
    public function __construct(DateService $dateService)
    {
        $this->setDateService($dateService);
    }
    /**
     * @param \DateTime $now
     * @param array     $options
     *
     * @return \DateTime[]
     */
    public function computeDelayInterval(\DateTime $now, $options = [])
    {
        $options += [
            'minDelayInDays' => 1,
            'maxDelayInDays' => 1,
            'businessDays'   => true,
            'holidays'       => [],
        ];

        return $this->getDateService()->computeIntervalInDays(
            $this->getDateService()->shiftDateOutsideHolidays($now, $options['holidays']),
            $options['minDelayInDays'],
            $options['maxDelayInDays'],
            true === $options['businessDays']
        );
    }
}
