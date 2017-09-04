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

use Exception;
use Itq\Common\Model;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\Plugin\PollerInterface;
use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\MicroserviceInterface;
use Itq\Common\Plugin\PollableSourceInterface;
use Itq\Common\Plugin\QueueCollectionInterface;
use Itq\Common\Plugin\IncomingPollableSourceInterface;
use Itq\Common\Plugin\OutgoingPollableSourceInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractMicroservice extends AbstractPlugin implements MicroserviceInterface
{
    use Traits\ServiceAware\PollerServiceAwareTrait;
    use Traits\ServiceAware\PollableSourceServiceAwareTrait;
    use Traits\ServiceAware\QueueCollectionServiceAwareTrait;
    /**
     * @param Service\PollerService          $pollerService
     * @param Service\PollableSourceService  $pollableSourceService
     * @param Service\QueueCollectionService $queueCollectionService
     * @param string                         $type
     * @param array                          $sources
     * @param array                          $options
     */
    public function __construct(
        Service\PollerService $pollerService,
        Service\PollableSourceService $pollableSourceService,
        Service\QueueCollectionService $queueCollectionService,
        $type,
        array $sources = [],
        array $options = []
    ) {
        $this->setType($type);
        $this->setPollerService($pollerService);
        $this->setPollableSourceService($pollableSourceService);
        $this->setQueueCollectionService($queueCollectionService);
        $this->initialize($sources, $options);
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->getParameter('type');
    }
    /**
     * @param array  $message
     * @param string $source
     *
     * @return Model\Internal\Result\ResultInterface|null|void
     *
     * @throws Exception if an error occured.
     */
    public function consume(array $message, $source)
    {
        unset($message, $source);
    }
    /**
     * Execute the idle when nothing to do
     *
     * @return void
     */
    public function idle()
    {
    }
    /**
     * Start the microservice (blocking method).
     *
     * @return $this
     *
     * @throws Exception
     */
    public function start()
    {
        while (true) {
            $found     = false;
            $poller    = $this->getPoller($this->getQueueCollection()->isEmpty() ? 'r' : 'rw');
            $available = $poller->poll();

            if (isset($available['r']) && is_array($available['r']) && 0 < count($available['r'])) {
                foreach ($available['r'] as $sourceName => $pollableSource) {
                    if (!($pollableSource instanceof IncomingPollableSourceInterface)) {
                        continue;
                    }
                    $message = $pollableSource->receive();
                    try {
                        $result = $this->consume($message, $sourceName);
                        if (!($result instanceof Model\Internal\Result\ResultInterface)) {
                            $result = new Model\Internal\Result\SuccessResult($result);
                        }
                    } catch (Exception $e) {
                        $this->logConsumeException($e, $sourceName, $message, $pollableSource);
                        $result = new Model\Internal\Result\ExceptionResult($e);
                    }
                    if ($pollableSource->isWaitingForReply()) {
                        $this->send($sourceName, $result->serialize());
                    }
                }
                $found = true;
            }
            if (isset($available['w']) && is_array($available['w']) && 0 < count($available['w'])) {
                foreach ($available['w'] as $sourceName => $pollableSource) {
                    $message = $this->getQueueCollection()->unqueue($sourceName);
                    if (!($pollableSource instanceof OutgoingPollableSourceInterface)) {
                        continue;
                    }
                    $pollableSource->send($message);
                }
                $found = true;
            }
            if (false === $found) {
                $this->idle();
            }
        }

        return $this;
    }
    /**
     * @param array $sources
     * @param array $options
     *
     * @return $this
     */
    protected function initialize(array $sources, array $options)
    {
        $this->initializePollers(['r', 'w', 'rw'], $options);
        $this->initializeSources($sources, $options);
        $this->initializeQueueCollection($options);

        return $this;
    }
    /**
     * @param string $type
     *
     * @return $this
     */
    protected function setType($type)
    {
        return $this->setParameter('type', $type);
    }
    /**
     * @param array $types
     * @param array $options
     *
     * @return $this
     */
    protected function initializePollers(array $types, array $options = [])
    {
        foreach ($types as $type) {
            $this->addPoller(
                $type,
                $this->getPollerService()->createPoller($this->getType(), ['name' => $type], $options)
            );
        }

        return $this;
    }
    /**
     * @param array $sources
     * @param array $options
     *
     * @return $this
     */
    protected function initializeSources(array $sources, array $options = [])
    {
        foreach ($sources as $sourceName => $sourceDefinition) {
            $source = $this->getPollableSourceService()->createSource(
                $this->getType(),
                $sourceDefinition + ['name' => $sourceName],
                $options
            );
            if ($source instanceof IncomingPollableSourceInterface) {
                $this->getPoller('r')->add($sourceName, $source);
            }
            if ($source instanceof OutgoingPollableSourceInterface) {
                $this->getPoller('w')->add($sourceName, $source);
            }
            $this->getPoller('rw')->add($sourceName, $source);
            $this->addSource($sourceName, $source);
        }

        return $this;
    }
    /**
     * @param array $options
     *
     * @return $this
     */
    protected function initializeQueueCollection(array $options = [])
    {
        unset($options);

        return $this->setQueueCollection(
            $this->getQueueCollectionService()->createQueueCollection('memory')
        );
    }
    /**
     * @param QueueCollectionInterface $queueCollection
     *
     * @return $this
     */
    protected function setQueueCollection(QueueCollectionInterface $queueCollection)
    {
        return $this->setService('queueCollection', $queueCollection);
    }
    /**
     * @param string          $name
     * @param PollerInterface $poller
     *
     * @return $this
     */
    protected function addPoller($name, PollerInterface $poller)
    {
        return $this->setArrayParameterKey('pollers', $name, $poller);
    }
    /**
     * @param string                  $name
     * @param PollableSourceInterface $source
     *
     * @return $this
     */
    protected function addSource($name, PollableSourceInterface $source)
    {
        return $this->setArrayParameterKey('sources', $name, $source);
    }
    /**
     * @param string $name
     *
     * @return PollerInterface
     */
    protected function getPoller($name)
    {
        return $this->getArrayParameterKey('pollers', $name);
    }
    /**
     * @return QueueCollectionInterface
     */
    protected function getQueueCollection()
    {
        return $this->getService('queueCollection');
    }
    /**
     * @param Exception               $e
     * @param string                  $sourceName
     * @param mixed                   $message
     * @param PollableSourceInterface $pollableSource
     */
    protected function logConsumeException(
        /** @noinspection PhpUnusedParameterInspection */ Exception $e,
        /** @noinspection PhpUnusedParameterInspection */ $sourceName,
        /** @noinspection PhpUnusedParameterInspection */ $message,
        /** @noinspection PhpUnusedParameterInspection */ PollableSourceInterface $pollableSource
    ) {
    }
    /**
     * @param string $target
     * @param mixed  $message
     *
     * @return $this
     */
    protected function send($target, $message)
    {
        $this->getQueueCollection()->queue($target, $message);

        return $this;
    }
}
