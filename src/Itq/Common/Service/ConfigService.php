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
use Itq\Common\Service;
use Itq\Common\ErrorManagerInterface;

/**
 * Config Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ConfigService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\EventServiceAwareTrait;
    /**
     * @param ErrorManagerInterface $errorManager
     * @param Service\EventService  $eventService
     */
    public function __construct(ErrorManagerInterface $errorManager, Service\EventService $eventService)
    {
        $this->setErrorManager($errorManager);
        $this->setEventService($eventService);
    }
    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->getErrorManager()->getErrorCodes();
    }
    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->getEventService()->getSequences();
    }
}
