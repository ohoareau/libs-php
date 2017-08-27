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
 * ModelRefresher Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelRefresherPluginAwareTrait
{
    /**
     * @param Plugin\ModelRefresherInterface $refresher
     *
     * @return $this
     */
    public function addModelRefresher(Plugin\ModelRefresherInterface $refresher)
    {
        return $this->pushArrayParameterItem('modelRefreshers', $refresher);
    }
    /**
     * @return Plugin\ModelRefresherInterface[]
     */
    public function getModelRefreshers()
    {
        return $this->getArrayParameter('modelRefreshers');
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
