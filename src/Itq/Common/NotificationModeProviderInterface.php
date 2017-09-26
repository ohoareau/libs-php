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
     * Set the specified notification mode
     *
     * @param string $mode
     *
     * @return $this
     */
    public function setCurrentMode($mode);
    /**
     * Check if specified mode is available
     *
     * @param string $mode
     *
     * @return void
     *
     * @throws Exception
     */
    public function checkMode($mode);
}
