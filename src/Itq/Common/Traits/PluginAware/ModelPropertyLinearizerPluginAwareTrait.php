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
 * ModelPropertyLinearizer Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelPropertyLinearizerPluginAwareTrait
{
    /**
     * @param Plugin\ModelPropertyLinearizerInterface $propertyLinearizer
     *
     * @return $this
     */
    public function addModelPropertyLinearizer(Plugin\ModelPropertyLinearizerInterface $propertyLinearizer)
    {
        return $this->pushArrayParameterItem('modelPropertyLinearizers', $propertyLinearizer);
    }
    /**
     * @return Plugin\ModelPropertyLinearizerInterface[]
     */
    public function getModelPropertyLinearizers()
    {
        return $this->getArrayParameter('modelPropertyLinearizers');
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
