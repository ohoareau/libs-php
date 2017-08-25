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

/**
 * Tracker Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface TrackerInterface
{
    /**
     * @param string $type
     * @param array  $definition
     * @param mixed  $data
     * @param array  $options
     *
     * @return $this
     */
    public function track($type, array $definition, $data, array $options = []);
}
