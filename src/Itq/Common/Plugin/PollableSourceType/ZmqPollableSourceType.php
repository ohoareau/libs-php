<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PollableSourceType;

use ZMQ;
use ZMQSocket;
use Exception;
use Itq\Common\Plugin\PollableSource\IncomingZmqSocketPollableSource;
use Itq\Common\Plugin\PollableSource\OutgoingZmqSocketPollableSource;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ZmqPollableSourceType extends Base\AbstractPollableTypeSource
{
    /**
     * @param array $definition
     * @param array $options
     * @return IncomingZmqSocketPollableSource|OutgoingZmqSocketPollableSource
     *
     * @throws Exception
     */
    public function create(array $definition = [], array $options = [])
    {
        switch ($definition['type']) {
            case ZMQ::SOCKET_PULL:
                return new IncomingZmqSocketPollableSource(
                    new ZMQSocket(
                        $options['context'],
                        ZMQ::SOCKET_PULL
                    )
                );
            case ZMQ::SOCKET_PUSH:
                return new OutgoingZmqSocketPollableSource(
                    new ZMQSocket(
                        $options['context'],
                        ZMQ::SOCKET_PUSH
                    )
                );
            default:
                throw $this->createFailedException(
                    "Unknown ZMQ pollable source type '%s'",
                    $definition['type']
                );
        }
    }
}
