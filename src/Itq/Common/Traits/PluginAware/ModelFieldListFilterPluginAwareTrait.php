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
 * ModelFieldListFilter Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelFieldListFilterPluginAwareTrait
{
    /**
     * @param Plugin\ModelFieldListFilterInterface $fieldListFilter
     *
     * @return $this
     */
    public function addModelFieldListFilter(Plugin\ModelFieldListFilterInterface $fieldListFilter)
    {
        return $this->pushArrayParameterItem('modelFieldListFilters', $fieldListFilter);
    }
    /**
     * @return Plugin\ModelFieldListFilterInterface[]
     */
    public function getModelFieldListFilters()
    {
        return $this->getArrayParameter('modelFieldListFilters');
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
