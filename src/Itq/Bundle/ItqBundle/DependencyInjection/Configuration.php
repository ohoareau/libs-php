<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class Configuration extends Base\AbstractConfiguration
{
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function buildTree(ArrayNodeDefinition $rootNode)
    {
        return $this
            ->addRootSection($rootNode)
            ->addShortLinkSection($rootNode)
            ->addPartnerTypesSection($rootNode)
            ->addAppsSection($rootNode)
            ->addDynamicUrlPatternsSection($rootNode)
            ->addSdkPhpSection($rootNode)
            ->addSdkJsSection($rootNode)
            ->addSendersSection($rootNode)
            ->addAnalyzedDirsSection($rootNode)
            ->addModelDescriptorDirsSection($rootNode)
            ->addRecipientsSection($rootNode)
            ->addEventsSection($rootNode)
            ->addBatchsSection($rootNode)
            ->addStoragesSection($rootNode)
            ->addPaymentProviderRulesSection($rootNode)
            ->addConnectionsSection($rootNode)
            ->addDataSection($rootNode)
        ;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addRootSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->scalarNode('tenant')->isRequired()->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addShortLinkSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('short_link')
            ->children()
            ->scalarNode('dns')->end()
            ->scalarNode('secret')->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addAppsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('apps')
            ->isRequired()
            ->prototype('array')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('url')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addPartnerTypesSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('partner_types')
            ->prototype('array')
            ->children()
            ->scalarNode('interface')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addDynamicUrlPatternsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->variableNode('dynamic_url_patterns')->defaultValue([])
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addSdkPhpSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('sdk_php')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('custom_template_dir')->end()
            ->scalarNode('company_name')->end()
            ->scalarNode('company_email')->end()
            ->scalarNode('package')->end()
            ->scalarNode('namespace')->end()
            ->scalarNode('start_year')->end()
            ->scalarNode('company_author_name')->end()
            ->scalarNode('company_author_email')->end()
            ->scalarNode('bundle_name')->end()
            ->scalarNode('bundle_key')->end()
            ->scalarNode('bundle_prefix')->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addSdkJsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('sdk_js')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('custom_template_dir')->end()
            ->scalarNode('company_name')->end()
            ->scalarNode('company_email')->end()
            ->scalarNode('package')->end()
            ->scalarNode('namespace')->end()
            ->scalarNode('start_year')->end()
            ->scalarNode('company_author_name')->end()
            ->scalarNode('company_author_email')->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addSendersSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('senders')
            ->prototype('array')
            ->prototype('array')
            ->beforeNormalization()
            ->always(function ($v) {
                if (!is_array($v)) {
                    $v = [];
                }
                if (!isset($v['sender'])) {
                    $v['sender'] = null;
                }
                if (!isset($v['name'])) {
                    $v['name'] = $v['sender'];
                }
                if (!isset($v['envs'])) {
                    $v += ['envs' => ['*']];
                }
                if (!isset($v['types'])) {
                    $v += ['types' => ['*']];
                }

                return $v;
            })
            ->end()
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('sender')->end()
            ->arrayNode('envs')
            ->requiresAtLeastOneElement()
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('types')
            ->requiresAtLeastOneElement()
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addModelDescriptorDirsSection(ArrayNodeDefinition $rootNode)
    {
        return $this->addTemplatedDirsSection($rootNode, 'model_descriptor_dirs');
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addAnalyzedDirsSection(ArrayNodeDefinition $rootNode)
    {
        return $this->addTemplatedDirsSection($rootNode, 'analyzed_dirs');
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     * @param string              $sectionName
     *
     * @return $this
     */
    protected function addTemplatedDirsSection(ArrayNodeDefinition $rootNode, $sectionName)
    {
        $rootNode
            ->children()
            ->arrayNode($sectionName)
            ->prototype('scalar')
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addRecipientsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('recipients')
            ->prototype('array')
            ->prototype('array')
            ->beforeNormalization()
            ->always(function ($v) {
                if (!is_array($v)) {
                    $v = [];
                }
                if (!isset($v['envs'])) {
                    $v += ['envs' => ['*']];
                }
                if (!isset($v['types'])) {
                    $v += ['types' => ['*']];
                }

                return $v;
            })
            ->end()
            ->children()
            ->scalarNode('name')->end()
            ->arrayNode('envs')
            ->requiresAtLeastOneElement()
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('types')
            ->requiresAtLeastOneElement()
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addEventsSection(ArrayNodeDefinition $rootNode)
    {
        return $this->addTemplatedEventsSection($rootNode, 'events');
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addBatchsSection(ArrayNodeDefinition $rootNode)
    {
        return $this->addTemplatedEventsSection($rootNode, 'batchs');
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addStoragesSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('storages')
            ->prototype('variable')
            ->beforeNormalization()
            ->always(function ($v) {
                if (!is_array($v)) {
                    return [];
                }
                if (!isset($v['mount'])) {
                    $v['mount'] = '/';
                }

                if (!isset($v['type'])) {
                    $v['type'] = 'file';
                }

                return $v;
            })
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addPaymentProviderRulesSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('payment_provider_rules')
            ->prototype('variable')
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addConnectionsSection(ArrayNodeDefinition $rootNode)
    {
        return $this->addTemplatedGenericArraySection($rootNode, 'connections');
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return $this
     */
    protected function addDataSection(ArrayNodeDefinition $rootNode)
    {
        return $this->addTemplatedGenericArraySection($rootNode, 'data');
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     * @param string              $sectionName
     *
     * @return $this
     */
    protected function addTemplatedEventsSection(ArrayNodeDefinition $rootNode, $sectionName)
    {
        $rootNode
            ->children()
            ->arrayNode($sectionName)
            ->prototype('array')
            ->children()
            ->scalarNode('throwExceptions')->defaultTrue()->end()
            ->arrayNode('actions')
            ->prototype('array')
            ->beforeNormalization()
            ->always(
                function ($v) {
                    if (!is_array($v)) {
                        return [];
                    }
                    if (!isset($v['action'])) {
                        return ['params' => $v];
                    }
                    $action = $v['action'];
                    unset($v['action']);

                    return ['action' => $action, 'params' => $v];
                }
            )
            ->end()
            ->children()
            ->scalarNode('action')->end()
            ->variableNode('params')->defaultValue([])->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
    /**
     * @param ArrayNodeDefinition $rootNode
     * @param string              $sectionName
     *
     * @return $this
     */
    protected function addTemplatedGenericArraySection(ArrayNodeDefinition $rootNode, $sectionName)
    {
        $rootNode
            ->children()
            ->arrayNode($sectionName)
            ->prototype('variable')
            ->beforeNormalization()
            ->always(function ($v) {
                if (!is_array($v)) {
                    $v = [];
                }

                return $v;
            })
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $this;
    }
}
