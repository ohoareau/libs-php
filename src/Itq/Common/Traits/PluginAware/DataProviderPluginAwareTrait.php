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
 * Supervision Data Provider Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DataProviderPluginAwareTrait
{
    /**
     * @param string                       $type
     * @param Plugin\DataProviderInterface $dataProvider
     *
     * @return $this
     */
    public function addDataProvider($type, Plugin\DataProviderInterface $dataProvider)
    {
        return $this->pushArrayParameterKeyItem('dataProviders', $type, $dataProvider);
    }
    /**
     * @return array
     */
    public function getDataProviders()
    {
        return $this->getArrayParameter('dataProviders');
    }
    /**
     * @param string $type
     *
     * @return Plugin\DataProviderInterface[]
     */
    public function getDataProvidersByType($type)
    {
        return $this->getArrayParameterListKey('dataProviders', $type);
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
    abstract protected function pushArrayParameterKeyItem($name, $key, $item);
    /**
     * @param string $name
     * @param string $key
     *
     * @return array
     */
    abstract protected function getArrayParameterListKey($name, $key);
}
