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
 * ModelCleaner Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelCleanerPluginAwareTrait
{
    /**
     * @param Plugin\ModelCleanerInterface $cleaner
     *
     * @return $this
     */
    public function addModelCleaner(Plugin\ModelCleanerInterface $cleaner)
    {
        return $this->pushArrayParameterItem('modelCleaners', $cleaner);
    }
    /**
     * @return Plugin\ModelCleanerInterface[]
     */
    public function getModelCleaners()
    {
        return $this->getArrayParameter('modelCleaners');
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
