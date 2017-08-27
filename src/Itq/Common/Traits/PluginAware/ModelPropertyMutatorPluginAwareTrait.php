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
 * ModelPropertyMutator Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelPropertyMutatorPluginAwareTrait
{
    /**
     * @param Plugin\ModelPropertyMutatorInterface $propertyMutator
     *
     * @return $this
     */
    public function addModelPropertyMutator(Plugin\ModelPropertyMutatorInterface $propertyMutator)
    {
        return $this->pushArrayParameterItem('modelPropertyMutators', $propertyMutator);
    }
    /**
     * @return Plugin\ModelPropertyMutatorInterface[]
     */
    public function getModelPropertyMutators()
    {
        return $this->getArrayParameter('modelPropertyMutators');
    }
    /**
     * @param string $name
     * @param mixed  $item
     *
     * @return $this
     */
    abstract protected function pushArrayParameterItem($name, $item);
    /**
     * @param string $name
     *
     * @return array
     */
    abstract protected function getArrayParameter($name);
}
