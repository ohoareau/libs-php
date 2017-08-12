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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FirstCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /** @var Service\PreprocessorService $preprocessor */
        $preprocessor = $container->get('preprocessor.preprocessor');

        foreach ($container->findTaggedServiceIds('preprocessor.conditionalbefore') as $id => $attrs) {
            foreach ($attrs as $params) {
                unset($params);
                /** @noinspection PhpParamsInspection */
                $preprocessor->addConditionalBeforeProcessor($container->get($id));
            }
        }

        $preprocessor->beforeProcess($container);
    }
}
