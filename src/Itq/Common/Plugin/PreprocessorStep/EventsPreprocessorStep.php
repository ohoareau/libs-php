<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PreprocessorStep;

use Itq\Common\PreprocessorContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class EventsPreprocessorStep extends Base\AbstractPreprocessorStep
{
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('app.event')) {
            return;
        }

        $ea     = $container->getDefinition('app.event');
        $events = $container->getParameter('app_events');
        foreach ($container->getParameter('app_batchs') as $eventName => $info) {
            $events['batchs_'.$eventName] = $info;
        }
        foreach ($events as $eventName => $info) {
            $eventName = false === strpos($eventName, '.') ? str_replace('_', '.', $eventName) : $eventName;
            $ea->addTag('kernel.event_listener', ['event' => $eventName, 'method' => 'consume']);
            $generalOptions = isset($info['throwExceptions']) ? ['throwException' => $info['throwExceptions']] : [];
            foreach ($info['actions'] as $a) {
                $options = $a;
                unset($options['action'], $options['params']);
                $ea->addMethodCall('register', [$eventName, $a['action'], $a['params'], $options + $generalOptions]);
            }
        }
    }
}
