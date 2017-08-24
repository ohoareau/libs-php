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
 * AnnotationProcessor Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait AnnotationProcessorPluginAwareTrait
{
    /**
     * @param string                              $type
     * @param Plugin\AnnotationProcessorInterface $processor
     *
     * @return $this
     */
    public function addAnnotationProcessor($type, Plugin\AnnotationProcessorInterface $processor)
    {
        return $this->pushArrayParameterKeyItem($type.'AnnotationProcessors', $processor->getAnnotationClass(), $processor);
    }
    /**
     * @param string $type
     * @param string $class
     *
     * @return Plugin\AnnotationProcessorInterface[]
     */
    public function getAnnotationProcessorsForClass($type, $class)
    {
        return $this->getArrayParameterListKey($type.'AnnotationProcessors', $class);
    }
    /**
     * @param string $name
     * @param string $key
     *
     * @return array
     */
    abstract protected function getArrayParameterListKey($name, $key);
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $item
     *
     * @return $this
     */
    abstract protected function pushArrayParameterKeyItem($name, $key, $item);
}
