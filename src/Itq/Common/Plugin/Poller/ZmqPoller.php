<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Poller;

use ZMQ;
use ZMQPoll;
use ZMQSocket;
use Exception;
use Itq\Common\Plugin\PollableSourceInterface;
use Itq\Common\Plugin\PollableSource\Base\AbstractZmqSocketPollableSource;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ZmqPoller extends Base\AbstractPoller
{
    /**
     * @param ZMQPoll $poller
     * @param array   $options
     */
    public function __construct(ZMQPoll $poller, array $options = [])
    {
        $options += ['timeout' => null];

        $this->setPoller($poller);
        $this->setOptions($options);
    }
    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        return $this->setParameter('options', $options);
    }
    /**
     * @param ZMQPoll $poller
     *
     * @return $this
     */
    public function setPoller(ZMQPoll $poller)
    {
        return $this->setService('poller', $poller);
    }
    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->getArrayParameter('options');
    }
    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return $this->getArrayParameterKeyIfExists('options', $name, $default);
    }
    /**
     * @return ZMQPoll
     */
    public function getPoller()
    {
        return $this->getService('poller');
    }
    /**
     * @param string                  $name
     * @param PollableSourceInterface $source
     *
     * @throws Exception
     */
    public function add($name, PollableSourceInterface $source)
    {
        if (!($source instanceof AbstractZmqSocketPollableSource)) {
            throw $this->createMalformedException(
                "Pollable source is not a ZMQ pollable source (class: %s)",
                get_class($source)
            );
        }

        $socket = $source->getSocket();

        $this->setArrayParameterKey('sockets', $name, $socket);
        $this->getPoller()->add($socket, $this->getSocketPollType($socket));
    }
    /**
     * @return array
     */
    public function poll()
    {
        $available = ['r' => [], 'w' => []];
        $readable  = [];
        $writable  = [];

        $events = $this->getPoller()->poll($readable, $writable, (int) $this->getOption('timeout', -1));

        if (0 >= $events) {
            return $available;
        }

        foreach ($readable as $socket) {
            list ($name, $source) = $this->getSourceBySocket($socket);
            $available['r'][$name] = $source;
        }
        foreach ($writable as $socket) {
            list ($name, $source) = $this->getSourceBySocket($socket);
            $available['w'][$name] = $source;
        }

        return $available;
    }
    /**
     * @param ZMQSocket $socket
     *
     * @return int
     *
     * @throws Exception
     */
    protected function getSocketPollType(ZMQSocket $socket)
    {
        switch ($socket->getSocketType()) {
            case ZMQ::SOCKET_PUB:
                return ZMQ::POLL_OUT;
            case ZMQ::SOCKET_SUB:
                return ZMQ::POLL_IN;
            case ZMQ::SOCKET_PULL:
                return ZMQ::POLL_IN;
            case ZMQ::SOCKET_PUSH:
                return ZMQ::POLL_OUT;
            case ZMQ::SOCKET_REP:
                return ZMQ::POLL_OUT;
            case ZMQ::SOCKET_REQ:
                return ZMQ::POLL_IN;
            default:
                throw $this->createFailedException(
                    "Unsupported ZMQ socket type '%d'",
                    $socket->getSocketType()
                );
        }
    }
    /**
     * @param ZMQSocket $socket
     *
     * @return array
     *
     * @throws Exception
     */
    protected function getSourceBySocket(ZMQSocket $socket)
    {
        $source = null;

        foreach ($this->getArrayParameter('sockets') as $socketName => $expectedPollableSocket) {
            if (!($expectedPollableSocket instanceof AbstractZmqSocketPollableSource)) {
                continue;
            }
            if ($expectedPollableSocket->getSocket() === $socket) {
                return [$socketName, $expectedPollableSocket];
            }
        }

        throw $this->createNotFoundException('No sources linked to socket');
    }
}
