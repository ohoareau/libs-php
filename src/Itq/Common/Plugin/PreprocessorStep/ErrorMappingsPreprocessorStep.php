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
class ErrorMappingsPreprocessorStep extends Base\AbstractPreprocessorStep
{
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return void
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        $mappings = $container->hasParameter('app_error_mappings') ? $container->getParameter('app_error_mappings') : [];

        if (!is_array($mappings)) {
            $mappings = [];
        }

        foreach ($mappings as $mapping) {
            if (!is_array($mapping) || !count($mapping)) {
                continue;
            }
            $container->getDefinition('app.errormanager')->addMethodCall('addKeyCodeMapping', [$mapping]);
        }
    }
}
