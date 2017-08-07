<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Event Dispatcher Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait EventDispatcherAwareTrait
{
    /**
     * @param string $key
     * @param mixed  $service
     *
     * @return $this
     */
    protected abstract function setService($key, $service);
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getService($key);
    /**
     * @param string $key
     *
     * @return bool
     */
    protected abstract function hasService($key);
    /**
     * @param EventDispatcherInterface $service
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $service)
    {
        return $this->setService('eventDispatcher', $service);
    }
    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->getService('eventDispatcher');
    }
    /**
     * @return bool
     */
    public function hasEventDispatcher()
    {
        return $this->hasService('eventDispatcher');
    }
    /**
     * @param string $event
     *
     * @return bool
     */
    protected function hasListeners($event)
    {
        return $this->getEventDispatcher()->hasListeners($event);
    }
    /**
     * @param string $event
     * @param null   $data
     *
     * @return $this
     */
    protected function dispatch($event, $data = null)
    {
        $this->getEventDispatcher()->dispatch($event, $data instanceof Event ? $data : new GenericEvent($data));

        return $this;
    }
}
