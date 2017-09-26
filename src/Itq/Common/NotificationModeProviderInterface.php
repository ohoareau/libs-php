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

use Exception;

/**
 * Notification Mode Provider Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface NotificationModeProviderInterface
{
    /**
     * @param string $type
     * @param string $default
     *
     * @return string
     */
    public function getTypeMode($type, $default = 'default');
    /**
     * @param string $type
     * @param string $mode
     *
     * @return $this
     */
    public function setTypeMode($type, $mode);
    /**
     * @param string $type
     * @param mixed  $data
     * @param array  $options
     *
     * @return $this
     */
    public function registerNotification($type, $data, array $options = []);
    /**
     * @return array
     */
    public function getRegisteredNotifications();
    /**
     * @param string $type
     *
     * @return array
     */
    public function getRegisteredNotificationByType($type);
}
