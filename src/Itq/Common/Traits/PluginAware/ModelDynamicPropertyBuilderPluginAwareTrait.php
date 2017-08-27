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
 * ModelDynamicPropertyBuilder Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelDynamicPropertyBuilderPluginAwareTrait
{
    /**
     * @param Plugin\ModelDynamicPropertyBuilderInterface $dynamicPropertyBuilder
     *
     * @return $this
     */
    public function addModelDynamicPropertyBuilder(Plugin\ModelDynamicPropertyBuilderInterface $dynamicPropertyBuilder)
    {
        return $this->pushArrayParameterItem('modelDynamicPropertyBuilders', $dynamicPropertyBuilder);
    }
    /**
     * @return Plugin\ModelDynamicPropertyBuilderInterface[]
     */
    public function getModelDynamicPropertyBuilders()
    {
        return $this->getArrayParameter('modelDynamicPropertyBuilders');
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
