<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\PluginAware;

use Itq\Common\Plugin;

/**
 * PollerType Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PollerTypePluginAwareTrait
{
    /**
     * @param string                     $type
     * @param Plugin\PollerTypeInterface $pollerType
     *
     * @return $this
     */
    public function addPollerType($type, Plugin\PollerTypeInterface $pollerType)
    {
        return $this->setArrayParameterKey('pollerTypes', $type, $pollerType);
    }
    /**
     * @return Plugin\PollerTypeInterface[]
     */
    public function getPollerTypes()
    {
        return $this->getArrayParameter('pollerTypes');
    }
    /**
     * @param string $type
     *
     * @return Plugin\PollerTypeInterface
     */
    public function getPollerType($type)
    {
        return $this->getArrayParameterKey('pollerTypes', $type);
    }
    /**
     * @param string $name
     *
     * @return array
     */
    abstract protected function getArrayParameter($name);
    /**
     * @param string $name
     * @param string $key
     *
     * @return mixed
     */
    abstract protected function getArrayParameterKey($name, $key);
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    abstract protected function setArrayParameterKey($name, $key, $value);
}
