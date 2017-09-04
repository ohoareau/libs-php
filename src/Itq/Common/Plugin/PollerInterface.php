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
 * Poller Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface PollerInterface
{
    /**
     * @param string                  $name
     * @param PollableSourceInterface $source
     *
     * @return void
     */
    public function add($name, PollableSourceInterface $source);
    /**
     * @return array
     */
    public function poll();
}
