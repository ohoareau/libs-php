<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Microservice\Base;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AbstractDispatcherMicroservice extends AbstractMicroservice
{
    /**
     * Consumes the incoming message.
     *
     * @param array  $message
     * @param string $source
     *
     * @return void
     */
    public function consume(array $message, $source)
    {
        $message = $this->processMessage($message, $source);

        if (null === $message) {
            return;
        }

        $type = $message['_type'];
        unset($message['_type']);

        $this->send($this->getTargetByType($type), $message);
    }
    /**
     * @param string $type
     *
     * @return string
     */
    protected function getTargetByType($type)
    {
        return sprintf('outgoing_%s', $type);
    }
    /**
     * @param array  $message
     * @param string $source
     *
     * @return array|null
     */
    protected function processMessage(array $message, $source)
    {
        unset($source);

        return $message;
    }
}
