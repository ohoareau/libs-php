<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\DependencyInjection\Base;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $this->buildTree($treeBuilder->root($this->getRootName()));

        return $treeBuilder;
    }
    /**
     * @return string
     */
    protected function getRootName()
    {
        return strtolower(preg_replace('/Bundle$/', '', basename(dirname(dirname(str_replace('\\', '/', get_class($this)))))));
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function buildTree(/** @noinspection PhpUnusedParameterInspection */ ArrayNodeDefinition $rootNode)
    {
        return $this;
    }
}
