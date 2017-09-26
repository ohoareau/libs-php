<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Itq\Common\NotificationModeProviderInterface;

/**
 * NotificationModeProviderAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait NotificationModeProviderAwareTrait
{
    /**
     * @param string $key
     * @param mixed  $service
     *
     * @return $this
     */
    protected abstract function setService($key, $service);
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getService($key);
    /**
     * @param NotificationModeProviderInterface $service
     *
     * @return $this
     */
    public function setNotificationModeProvider(NotificationModeProviderInterface $service)
    {
        return $this->setService('notificationModeProvider', $service);
    }
    /**
     * @return NotificationModeProviderInterface
     */
    public function getNotificationModeProvider()
    {
        return $this->getService('notificationModeProvider');
    }
}
