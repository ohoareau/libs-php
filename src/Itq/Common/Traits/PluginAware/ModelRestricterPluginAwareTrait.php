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
 * ModelRestricter Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelRestricterPluginAwareTrait
{
    /**
     * @param Plugin\ModelRestricterInterface $restricter
     *
     * @return $this
     */
    public function addModelRestricter(Plugin\ModelRestricterInterface $restricter)
    {
        return $this->pushArrayParameterItem('modelRestricters', $restricter);
    }
    /**
     * @return Plugin\ModelRestricterInterface[]
     */
    public function getModelRestricters()
    {
        return $this->getArrayParameter('modelRestricters');
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
