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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class Composer
{
    /**
     *
     */
    public static function install()
    {
        passthru('rm -rf app/cache/*/');
    }
    /**
     *
     */
    public static function update()
    {
        passthru('rm -rf app/cache/*/');
    }
}
