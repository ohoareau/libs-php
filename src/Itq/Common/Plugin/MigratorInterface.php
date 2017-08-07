<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

use Exception;

/**
 * Migrator Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface MigratorInterface
{
    /**
     * Process the upgrade path.
     *
     * @param string $path
     * @param array  $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function upgrade($path, $options = []);
}
