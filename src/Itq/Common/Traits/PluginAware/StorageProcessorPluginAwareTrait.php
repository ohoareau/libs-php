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
 * StorageProcessor Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait StorageProcessorPluginAwareTrait
{
    /**
     * @param Plugin\StorageProcessorInterface $processor
     *
     * @return $this
     */
    public function addStorageProcessor(Plugin\StorageProcessorInterface $processor)
    {
        foreach (is_array($processor->getType()) ? $processor->getType() : [$processor->getType()] as $type) {
            $this->setArrayParameterKey('storageProcessors', $type, $processor);
        }

        return $this;
    }
    /**
     * @return Plugin\StorageProcessorInterface[]
     */
    public function getStorageProcessors()
    {
        return $this->getArrayParameter('storageProcessors');
    }
    /**
     * @param string $type
     *
     * @return Plugin\StorageProcessorInterface
     */
    public function getStorageProcessor($type)
    {
        return $this->getArrayParameterKey('storageProcessors', $type);
    }
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    abstract protected function setArrayParameterKey($name, $key, $value);
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
}
