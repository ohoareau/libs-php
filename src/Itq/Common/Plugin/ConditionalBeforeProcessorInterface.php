<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ConditionalBeforeProcessorInterface
{
    /**
     * @return string|array
     */
    public function getCondition();
    /**
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     * @param string           $condition
     *
     * @return bool
     */
    public function isKept(array $params, $id, Definition $d, ContainerBuilder $container, $condition);
}
