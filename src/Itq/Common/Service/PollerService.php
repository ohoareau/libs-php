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

use Itq\Common\Plugin\PollerTypeInterface;
use Itq\Common\Traits;
use Itq\Common\Plugin\PollerInterface;

/**
 * Poller Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PollerService
{
    use Traits\ServiceTrait;
    /**
     * @param string $type
     * @param array  $definition
     * @param array  $options
     *
     * @return PollerInterface
     */
    public function createPoller($type, array $definition = [], array $options = [])
    {
        return $this->getPollerType($type)->create($definition, $options);
    }
    /**
     * @param string              $type
     * @param PollerTypeInterface $pollerType
     *
     * @return $this
     */
    public function addPollerType($type, PollerTypeInterface $pollerType)
    {
        return $this->setArrayParameterKey('pollerTypes', $type, $pollerType);
    }
    /**
     * @return PollerTypeInterface[]
     */
    public function getPollerTypes()
    {
        return $this->getArrayParameter('pollerTypes');
    }
    /**
     * @param string $type
     *
     * @return PollerTypeInterface
     */
    public function getPollerType($type)
    {
        return $this->getArrayParameterKey('pollerTypes', $type);
    }
}
