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
use Itq\Common\NotificationModeProviderInterface;

/**
 * NotificationMode Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NotificationModeService implements NotificationModeProviderInterface
{
    use Traits\ServiceTrait;
    /**
     * @param string $type
     * @param string $default
     *
     * @return string
     */
    public function getTypeMode($type, $default = 'default')
    {
        return $this->getArrayParameterKeyIfExists('typeModes', $type, $default);
    }
    /**
     * @param string $type
     * @param string $mode
     *
     * @return $this
     */
    public function setTypeMode($type, $mode)
    {
        return $this->setArrayParameterKey('typeModes', $type, $mode);
    }
    /**
     * @param string $type
     * @param mixed  $data
     * @param array  $options
     *
     * @return $this
     */
    public function registerNotification($type, $data, array $options = [])
    {
        return $this->pushArrayParameterKeyItem('notifications', $type, ['data' => $data, 'options' => $options]);
    }
    /**
     * @return array
     */
    public function getRegisteredNotifications()
    {
        return $this->getArrayParameter('notifications');
    }
    /**
     * @param string $type
     *
     * @return array
     */
    public function getRegisteredNotificationByType($type)
    {
        return $this->getArrayParameterListKey('notifications', $type);
    }
}
