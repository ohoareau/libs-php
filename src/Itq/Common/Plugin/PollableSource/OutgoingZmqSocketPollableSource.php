<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PollableSource;

use Itq\Common\Plugin\OutgoingPollableSourceInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class OutgoingZmqSocketPollableSource extends Base\AbstractZmqSocketPollableSource implements OutgoingPollableSourceInterface
{
    /**
     * @param array $message
     *
     * @return void
     */
    public function send($message)
    {
        $this->sendToSocket($this->getSocket(), $message);
    }
}
