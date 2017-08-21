<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelRefresher\Base;

use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\ModelRefresherInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractModelRefresher extends AbstractPlugin implements ModelRefresherInterface
{
    /**
     * @param mixed  $doc
     * @param string $property
     * @param array  $options
     *
     * @return bool
     */
    protected function isPopulableModelProperty($doc, $property, array $options = [])
    {
        return property_exists($doc, $property) && (!isset($options['populateNulls']) || (false === $options['populateNulls'] && null !== $doc->$property));
    }
    /**
     * @param array $options
     *
     * @return object
     */
    protected function createModelInstance(array $options)
    {
        $class = $options['model'];

        return new $class();
    }
}
