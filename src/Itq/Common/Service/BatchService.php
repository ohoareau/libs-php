<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Batch Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BatchService
{
    use Traits\ServiceTrait;
    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->setEventDispatcher($eventDispatcher);
    }
    /**
     * @param string $name
     * @param array  $params
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function execute($name, array $params = [], array $options = [])
    {
        $eventName = 'batchs.'.$name;

        if (!$this->hasListeners($eventName)) {
            throw $this->createNotFoundException("Unknown batch '%s'", $name);
        }

        return $this->dispatch($eventName, $params + ['options' => $options]);
    }
}
