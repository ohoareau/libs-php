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
 * Dispatch Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DispatchService
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
        if (!$this->hasListeners($name)) {
            throw $this->createNotFoundException("Unknown event of type '%s'", $name);
        }

        return $this->dispatch($name, $params + ['options' => $options]);
    }
}
