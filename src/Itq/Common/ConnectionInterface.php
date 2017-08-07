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
 * Connection Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ConnectionInterface
{
    /**
     * @return mixed
     */
    public function getBackend();
    /**
     * @return array
     */
    public function getParameters();
    /**
     * @param string $name
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getParameter($name, $defaultValue = null);
}
