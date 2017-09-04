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
 * Outgoing Pollable Source Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface OutgoingPollableSourceInterface extends PollableSourceInterface
{
    /**
     * @param mixed $message
     *
     * @return void
     */
    public function send($message);
}
