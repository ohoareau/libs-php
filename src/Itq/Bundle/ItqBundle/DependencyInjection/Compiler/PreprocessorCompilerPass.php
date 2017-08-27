<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\DependencyInjection\Compiler;

use Itq\Common\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PreprocessorCompilerPass extends Base\AbstractPreprocessorCompilerPass
{
    /**
     * @param Service\PreprocessorService $preprocessorService
     * @param ContainerBuilder            $container
     */
    protected function processPreprocessor(Service\PreprocessorService $preprocessorService, ContainerBuilder $container)
    {
        $steps = [];

        foreach ($this->findServiceTags($container, 'preprocessor.step') as $stepServiceTag) {
            $stepServiceDefinition = $container->getDefinition($stepServiceTag['serviceId']);
            switch (true) {
                case $stepServiceDefinition->hasTag('preprocessor.aware.contextdumpers'):
                    foreach ($this->findServiceTags($container, 'preprocessor.contextdumper') as $serviceTag) {
                        $stepServiceTag['service']->addContextDumper($serviceTag['service']);
                    }
                    break;
                case $stepServiceDefinition->hasTag('preprocessor.aware.tags'):
                    foreach ($this->findServiceTags($container, 'preprocessor.tag') as $serviceTag) {
                        $stepServiceTag['service']->addTagProcessor($serviceTag['service']);
                    }
                    break;
                case $stepServiceDefinition->hasTag('preprocessor.aware.storages'):
                    foreach ($this->findServiceTags($container, 'preprocessor.storage') as $serviceTag) {
                        $stepServiceTag['service']->addStorageProcessor($serviceTag['service']);
                    }
                    break;
                case $stepServiceDefinition->hasTag('preprocessor.aware.annotations'):
                    foreach ($this->findServiceTags($container, 'preprocessor.annotation') as $serviceTag) {
                        $stepServiceTag['service']->addAnnotationProcessor($serviceTag['service']);
                    }
                    break;
            }
            $steps[] = [$stepServiceTag['params']['priority'], $stepServiceTag['params']['id'], $stepServiceTag['service']];
        }

        $this->sortPriority($steps);

        foreach ($steps as $step) {
            $preprocessorService->addPreprocessorStep($step[1], $step[2]);
        }

        $preprocessorService->process($container);
    }
}
