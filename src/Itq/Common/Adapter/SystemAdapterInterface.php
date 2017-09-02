<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Adapter;

/**
 * System Adapter Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface SystemAdapterInterface
{
    /**
     * @return string
     */
    public function getTempDirectory();
    /**
     * @param string $command
     * @param array  $output
     * @param int    $return
     *
     * @return string
     */
    public function exec($command, array &$output, &$return);
    /**
     * @param string $command
     * @param int    $return
     *
     * @return void
     */
    public function passthru($command, &$return);
    /**
     * @return float
     */
    public function microtime();
    /**
     * @return string
     */
    public function hostname();
}
