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

use Exception;
use Itq\Common\Plugin\IncomingPollableSourceInterface;
use Itq\Common\Plugin\OutgoingPollableSourceInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryPollableSource extends Base\AbstractPollableSource implements IncomingPollableSourceInterface, OutgoingPollableSourceInterface
{
    /**
     * @param array    $in
     * @param array    $out
     * @param bool     $waitingForReply
     * @param int|null $virtualTimeout
     */
    public function __construct(array $in = [], array $out = [], $waitingForReply = false, $virtualTimeout = null)
    {
        $this->setParameter('in', $in);
        $this->setParameter('out', $out);
        $this->setBoolParameter('waitingForReply', $waitingForReply);
        $this->setTimeout($virtualTimeout);
    }
    /**
     * @param int|null $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        return $this->setParameter('timeout', null === $timeout ? null : (int) $timeout);
    }
    /**
     * @return int|null
     */
    public function getTimeout()
    {
        return $this->getParameterIfExists('timeout');
    }
    /**
     * @return bool
     */
    public function isWaitingForReply()
    {
        return $this->getBoolParameter('waitingForReply');
    }
    /**
     * @return mixed
     */
    public function receive()
    {
        return $this->popArrayParameterItem('in');
    }
    /**
     * @param mixed $message
     */
    public function send($message)
    {
        $this->pushArrayParameterItem('out', $message);
    }
    /**
     * @param int|null $timeout
     *
     * @return array
     *
     * @throws Exception
     */
    public function testPendings($timeout = null)
    {
        $virtualTimeout = $this->getTimeout();

        if (null !== $virtualTimeout) {
            if (null !== $timeout && ($timeout < $virtualTimeout)) {
                throw $this->createFailedException('Timeout (%d) occured for pollable source', $timeout);
            }
        }

        return [$this->countArrayParameterItems('in'), $this->countArrayParameterItems('out')];
    }
    /**
     * @param mixed $message
     *
     * @return $this
     */
    public function pushIncoming($message)
    {
        return $this->pushArrayParameterItem('in', $message);
    }
    /**
     * @return mixed
     */
    public function popOutgoing()
    {
        return $this->popArrayParameterItem('out');
    }
    /**
     * @return mixed[]
     */
    public function flushOutgoings()
    {
        return $this->flushArrayParameterItems('out');
    }
}
