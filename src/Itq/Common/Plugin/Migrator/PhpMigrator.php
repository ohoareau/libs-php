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
class PhpMigrator extends Base\AbstractMigrator
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
        $container  = $this->getContainer();
        $logger     = $this->getLogger();
        $dispatcher = $this->getEventDispatcher();

        if (!is_file($path)) {
            throw $this->createNotFoundException("Unknown PHP Diff file '%s'", $path);
        }

        /** @noinspection PhpIncludeInspection */
        include $path;

        unset($options);
        unset($container);
        unset($logger);
        unset($dispatcher);

        return $this;
    }
}
