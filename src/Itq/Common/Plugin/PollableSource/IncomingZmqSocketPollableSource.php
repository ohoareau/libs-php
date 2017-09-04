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

use Itq\Common\Plugin\IncomingPollableSourceInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class IncomingZmqSocketPollableSource extends Base\AbstractZmqSocketPollableSource implements IncomingPollableSourceInterface
{
    /**
     * @return array
     */
    public function receive()
    {
        return $this->receiveFromSocket($this->getSocket());
    }
}
