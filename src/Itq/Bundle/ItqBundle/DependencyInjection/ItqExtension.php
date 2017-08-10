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
            $config['analyzed_dirs']
        );

        $container->setParameter('app_sdk_php', isset($config['sdk_php']) ? $config['sdk_php'] : null);
        $container->setParameter('app_sdk_js', isset($config['sdk_js']) ? $config['sdk_js'] : null);
        $container->setParameter('app_sdk_php_custom_template_dir', isset($config['sdk_php']['custom_template_dir']) ? $config['sdk_php']['custom_template_dir'] : null);
        $container->setParameter('app_sdk_js_custom_template_dir', isset($config['sdk_js']['custom_template_dir']) ? $config['sdk_js']['custom_template_dir'] : null);
        $container->setParameter('app_senders', $config['senders']);
        $container->setParameter('app_recipients', $config['recipients']);
        $container->setParameter('app_analyzed_dirs', $config['analyzed_dirs']);
        $container->setParameter('app_events', $config['events']);
        $container->setParameter('app_batchs', $config['batchs']);
        $container->setParameter('app_storages', $config['storages']);
        $container->setParameter('app_connections', $config['connections']);
        $container->setParameter('app_payment_provider_rules', $config['payment_provider_rules']);
        $container->setParameter('app_dynamic_url_patterns', $config['dynamic_url_patterns']);
    }
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function finishApply(array $config, ContainerBuilder $container)
    {
        if (isset($config['partner_types']) && is_array($config['partner_types'])) {
            foreach ($config['partner_types'] as $partnerType => $partnerTypeInfo) {
                $container->getDefinition('app.partner')->addMethodCall('registerType', [$partnerType, $partnerTypeInfo]);
            }
        }
    }
    /**
     * @return array
     */
    protected function getLoadableFiles()
    {
        return array_merge(parent::getLoadableFiles(), ['plugins.yml', 'form-types.yml']);
    }
}
