<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Migrator;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ShMigrator extends Base\AbstractMigrator
{
    /**
     * @param string $path
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function upgrade($path, $options = [])
    {
        if (!is_file($path)) {
            throw $this->createNotFoundException("Unknown SH Diff file '%s'", $path);
        }

        passthru(sprintf('sh %s 2>&1', $path));

        unset($options);

        return $this;
    }
}
