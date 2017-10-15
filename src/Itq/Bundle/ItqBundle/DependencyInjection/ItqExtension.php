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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ItqExtension extends Base\AbstractExtension
{
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function preApply(array $config, ContainerBuilder $container)
    {
        $container->setParameter(
            'app_variables',
            [
                'env'        => $container->hasParameter('app_env') ? $container->getParameter('app_env') : 'unknown',
                'apps'       => $config['apps'],
                'senders'    => $config['senders'],
                'sdk'        => isset($config['sdk_php']) ? $config['sdk_php'] : null, // for compatibility purpose
                'sdk_php'    => isset($config['sdk_php']) ? $config['sdk_php'] : null,
                'sdk_js'     => isset($config['sdk_js']) ? $config['sdk_js'] : null,
                'tenant'     => $config['tenant'],
                'short_link' => $config['short_link'],
            ]
        );

        $config['analyzed_dirs'] = array_merge(
            [
                __DIR__.'/../../../Common/Model',
                __DIR__.'/../../../Common/Plugin',
            ],
            isset($config['analyzed_dirs']) ? $config['analyzed_dirs'] : []
        );

        $config['model_descriptor_dirs'] = array_merge(
            [
                __DIR__.'/../../../Common/Resources/models',
            ],
            isset($config['model_descriptor_dirs']) ? $config['model_descriptor_dirs'] : []
        );

        $container->setParameter('app_sdk_php', isset($config['sdk_php']) ? $config['sdk_php'] : null);
        $container->setParameter('app_sdk_js', isset($config['sdk_js']) ? $config['sdk_js'] : null);
        $container->setParameter('app_sdk_php_custom_template_dir', isset($config['sdk_php']['custom_template_dir']) ? $config['sdk_php']['custom_template_dir'] : null);
        $container->setParameter('app_sdk_js_custom_template_dir', isset($config['sdk_js']['custom_template_dir']) ? $config['sdk_js']['custom_template_dir'] : null);
        $container->setParameter('app_senders', $config['senders']);
        $container->setParameter('app_recipients', $config['recipients']);
        $container->setParameter('app_analyzed_dirs', $config['analyzed_dirs']);
        $container->setParameter('app_model_descriptor_dirs', $config['model_descriptor_dirs']);
        $container->setParameter('app_events', $config['events']);
        $container->setParameter('app_batchs', $config['batchs']);
        $container->setParameter('app_storages', $config['storages']);
        $container->setParameter('app_connections', $config['connections']);
        $container->setParameter('app_payment_provider_rules', $config['payment_provider_rules']);
        $container->setParameter('app_dynamic_url_patterns', $config['dynamic_url_patterns']);
        $container->setParameter('__itq_data_providers', $config['data']);
    }
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function finishApply(array $config, ContainerBuilder $container)
    {
        if (isset($config['partner_types']) && is_array($config['partner_types'])) {
            foreach ($config['partner_types'] as $partnerType => $partnerTypeInfo) {
                $container->getDefinition('app.servicepartner')->addMethodCall('registerType', [$partnerType, $partnerTypeInfo]);
            }
        }
    }
    /**
     * @return array
     */
    protected function getLoadableFiles()
    {
        return [
            'preprocessor/common.yml',
            'preprocessor/steps.yml',
            'preprocessor/before-steps.yml',
            'preprocessor/context-dumpers.yml',
            'preprocessor/processors/annotations.yml',
            'preprocessor/processors/conditional-befores.yml',
            'preprocessor/processors/storages.yml',
            'preprocessor/processors/tags.yml',
            'services.yml',
            'commands.yml',
            'validators.yml',
            'plugins/http-protocol-handlers.yml',
            'plugins/generators.yml',
            'plugins/migrators.yml',
            'plugins/actions.yml',
            'plugins/code-generators.yml',
            'plugins/connection-bags.yml',
            'plugins/data-collectors.yml',
            'plugins/document-builders.yml',
            'plugins/exception-descriptors.yml',
            'plugins/formatters.yml',
            'plugins/rule-types.yml',
            'plugins/data-filters.yml',
            'plugins/type-guess-builders.yml',
            'plugins/unique-code-generator-algorithms.yml',
            'plugins/model/cleaners.yml',
            'plugins/model/dynamic-property-builders.yml',
            'plugins/model/property-mutators.yml',
            'plugins/model/refreshers.yml',
            'plugins/model/restricters.yml',
            'plugins/model/update-enrichers.yml',
            'plugins/model/field-list-filters.yml',
            'plugins/model/property-linearizers.yml',
            'plugins/model/property-authorization-checkers.yml',
            'plugins/criterium-types.yml',
            'plugins/data-providers.yml',
            'plugins/request-codecs.yml',
            'plugins/trackers.yml',
            'plugins/converters.yml',
            'plugins/model-descriptors.yml',
            'form/types.yml',
            'form/type-guessers.yml',
        ];
    }
}
