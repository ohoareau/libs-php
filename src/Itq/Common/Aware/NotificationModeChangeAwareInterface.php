<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware;

use Itq\Common\Model;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface NotificationModeChangeAwareInterface
{
    /**
     * @param Model\Internal\NotificationMode $notificationMode
     * @param array                           $options
     *
     * @return void
     */
    public function changeNotificationMode(Model\Internal\NotificationMode $notificationMode, array $options = []);
}
