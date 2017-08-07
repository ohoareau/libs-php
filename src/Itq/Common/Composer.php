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

use Composer\Script\Event;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class Composer
{
    /**
     * @param Event $event
     */
    public static function install(Event $event)
    {
        passthru('rm -rf app/cache/*/');
    }
    /**
     * @param Event $event
     */
    public static function update(Event $event)
    {
        passthru('rm -rf app/cache/*/');
    }
}
