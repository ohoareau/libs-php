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
        foreach ($this->findServiceTags($container, 'preprocessor.conditionalbefore') as $serviceTag) {
            /** @noinspection PhpParamsInspection */
            $preprocessorService->addConditionalBeforeProcessor($serviceTag['service']);
        }

        $preprocessorService->beforeProcess($container);
    }
}
