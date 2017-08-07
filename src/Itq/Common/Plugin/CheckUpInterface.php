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
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface CheckUpInterface
{
    /**
     * @param array $vars
     * @param array $options
     *
     * @return $this
     */
    public function checkUp(array $vars = [], array $options = []);
}
