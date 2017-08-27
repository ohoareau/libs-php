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
 * ModelPropertyAuthorizationChecker Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelPropertyAuthorizationCheckerPluginAwareTrait
{
    /**
     * @param Plugin\ModelPropertyAuthorizationCheckerInterface $propertyAuthorizationChecker
     *
     * @return $this
     */
    public function addModelPropertyAuthorizationChecker(Plugin\ModelPropertyAuthorizationCheckerInterface $propertyAuthorizationChecker)
    {
        return $this->pushArrayParameterItem('modelPropertyAuthorizationCheckers', $propertyAuthorizationChecker);
    }
    /**
     * @return Plugin\ModelPropertyAuthorizationCheckerInterface[]
     */
    public function getModelPropertyAuthorizationCheckers()
    {
        return $this->getArrayParameter('modelPropertyAuthorizationCheckers');
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
