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
class FirstCompilerPass extends Base\AbstractPreprocessorCompilerPass
{
    /**
     * @param Service\PreprocessorService $preprocessorService
     * @param ContainerBuilder            $container
     */
    protected function processPreprocessor(Service\PreprocessorService $preprocessorService, ContainerBuilder $container)
    {
        $steps = [];

        foreach ($this->findServiceTags($container, 'preprocessor.before_step') as $stepServiceTag) {
            $stepServiceDefinition = $container->getDefinition($stepServiceTag['serviceId']);
            switch (true) {
                case $stepServiceDefinition->hasTag('preprocessor.aware.conditionals'):
                    foreach ($this->findServiceTags($container, 'preprocessor.conditionalbefore') as $serviceTag) {
                        $stepServiceTag['service']->addConditionalBeforeProcessor($serviceTag['service']);
                    }
                    break;
            }
            $steps[] = [$stepServiceTag['params']['priority'], $stepServiceTag['params']['id'], $stepServiceTag['service']];
        }

        usort(
            $steps,
            function ($a, $b) {
                return ($a[0] > $b[0]) ? -1 : (($a[0] === $b[0]) ? 0 : 1);
            }
        );

        foreach ($steps as $step) {
            $preprocessorService->addBeforeStep($step[1], $step[2]);
        }

        $preprocessorService->beforeProcess($container);
    }
}
