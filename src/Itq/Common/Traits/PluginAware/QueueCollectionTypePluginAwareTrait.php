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
 * QueueCollectionType Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait QueueCollectionTypePluginAwareTrait
{
    /**
     * @param string                              $type
     * @param Plugin\QueueCollectionTypeInterface $queueCollectionType
     *
     * @return $this
     */
    public function addQueueCollectionType($type, Plugin\QueueCollectionTypeInterface $queueCollectionType)
    {
        return $this->setArrayParameterKey('queueCollectionTypes', $type, $queueCollectionType);
    }
    /**
     * @return Plugin\QueueCollectionTypeInterface[]
     */
    public function getQueueCollectionTypes()
    {
        return $this->getArrayParameter('queueCollectionTypes');
    }
    /**
     * @param string $type
     *
     * @return Plugin\QueueCollectionTypeInterface
     */
    public function getQueueCollectionType($type)
    {
        return $this->getArrayParameterKey('queueCollectionTypes', $type);
    }
    /**
     * @param string $name
     *
     * @return array
     *
     * @throws Exception
     */
    abstract protected function getArrayParameter($name);
    /**
     * @param string $name
     * @param string $key
     *
     * @return mixed
     *
     * @throws Exception
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
