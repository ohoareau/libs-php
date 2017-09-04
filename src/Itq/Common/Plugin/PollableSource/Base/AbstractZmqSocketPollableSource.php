<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PollableSource\Base;

use ZMQ;
use ZMQSocket;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractZmqSocketPollableSource extends AbstractPollableSource
{
    /**
     * @param ZMQSocket $socket
     */
    public function __construct(ZMQSocket $socket)
    {
        $this->setSocket($socket);
    }
    /**
     * @param ZMQSocket $socket
     *
     * @return $this
     */
    public function setSocket(ZMQSocket $socket)
    {
        return $this->setService('socket', $socket);
    }
    /**
     * @return ZMQSocket
     */
    public function getSocket()
    {
        return $this->getService('socket');
    }
    /**
     * @return bool
     */
    public function isWaitingForReply()
    {
        return ZMQ::SOCKET_REP === $this->getSocket()->getSocketType();
    }
    /**
     * @param mixed $message
     *
     * @return array
     */
    protected function unserialize($message)
    {
        return json_decode($message, true);
    }
    /**
     * @param mixed $message
     *
     * @return string
     */
    protected function serialize($message)
    {
        return json_encode($message);
    }
    /**
     * @param ZMQSocket $socket
     *
     * @return array
     */
    protected function receiveFromSocket(ZMQSocket $socket)
    {
        return $this->unserialize($socket->recv());
    }
    /**
     * @param ZMQSocket $socket
     * @param array     $message
     *
     * @return void
     */
    protected function sendToSocket(ZMQSocket $socket, $message)
    {
        $socket->send($this->serialize($message));
    }
}
