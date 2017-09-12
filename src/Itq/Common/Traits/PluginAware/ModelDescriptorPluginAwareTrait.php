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
 * Model Descriptor Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelDescriptorPluginAwareTrait
{
    /**
     * @param string                          $type
     * @param Plugin\ModelDescriptorInterface $modelDescriptor
     *
     * @return $this
     */
    public function addModelDescriptor($type, Plugin\ModelDescriptorInterface $modelDescriptor)
    {
        return $this->setArrayParameterKey('modelDescriptors', $type, $modelDescriptor);
    }
    /**
     * @return Plugin\ModelDescriptorInterface[]
     */
    public function getModelDescriptors()
    {
        return $this->getArrayParameter('modelDescriptors');
    }
    /**
     * @param string $type
     *
     * @return Plugin\ModelDescriptorInterface
     */
    public function getModelDescriptor($type)
    {
        return $this->getArrayParameterKey('modelDescriptors', $type);
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
     * @param mixed  $item
     *
     * @return $this
     */
    abstract protected function setArrayParameterKey($name, $key, $item);
    /**
     * @param string $name
     * @param string $key
     *
     * @return mixed
     */
    abstract protected function getArrayParameterKey($name, $key);
}
