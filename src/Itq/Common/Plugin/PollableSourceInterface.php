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
 * Pollable Source Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface PollableSourceInterface
{
    /**
     * @return bool
     */
    public function isWaitingForReply();
    /**
     * @param int|null $timeout
     *
     * @return array
     */
    public function testPendings($timeout = null);
}
