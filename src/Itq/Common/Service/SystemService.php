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

/**
 * System Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SystemService
{
    use Traits\ServiceTrait;
    /**
     * @return string
     */
    public function getTempDirectory()
    {
        return sys_get_temp_dir();
    }
    /**
     * @param string $command
     *
     * @return string
     *
     * @throws \Exception
     */
    public function execute($command)
    {
        $output = [];
        $return = 0;

        exec($command, $output, $return);

        if (0 !== $return) {
            throw $this->createFailedException('Command [%s] failed with error code [%d]', $command, $return);
        }

        return join(PHP_EOL, $output);
    }
    /**
     * @param string $command
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function executeInForeground($command, array $options = [])
    {
        unset($options);

        $return = 0;

        passthru($command, $return);

        if (0 !== $return) {
            throw $this->createFailedException('Foreground command [%s] failed with error code [%d]', $command, $return);
        }

        return $this;
    }
}
