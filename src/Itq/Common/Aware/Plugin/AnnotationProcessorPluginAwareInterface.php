<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware\Plugin;

use Itq\Common\Plugin;

/**
 * Aware interface trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface AnnotationProcessorPluginAwareInterface
{
    /**
     * @param string                              $type
     * @param Plugin\AnnotationProcessorInterface $processor
     */
    public function addAnnotationProcessor($type, Plugin\AnnotationProcessorInterface $processor);
    /**
     * @param string $type
     * @param string $class
     *
     * @return Plugin\AnnotationProcessorInterface[]
     */
    public function getAnnotationProcessorsForClass($type, $class);
}
