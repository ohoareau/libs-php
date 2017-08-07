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
class PreprocessorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /** @var Service\PreprocessorService $preprocessor */
        $preprocessor = $container->get('preprocessor.preprocessor');

        foreach ($container->findTaggedServiceIds('preprocessor.contextdumper') as $id => $attrs) {
            foreach ($attrs as $params) {
                unset($params);
                /** @noinspection PhpParamsInspection */
                $preprocessor->addContextDumper($container->get($id));
            }
        }
        foreach ($container->findTaggedServiceIds('preprocessor.tag') as $id => $attrs) {
            foreach ($attrs as $params) {
                unset($params);
                /** @noinspection PhpParamsInspection */
                $preprocessor->addTagProcessor($container->get($id));
            }
        }
        foreach ($container->findTaggedServiceIds('preprocessor.storage') as $id => $attrs) {
            foreach ($attrs as $params) {
                unset($params);
                /** @noinspection PhpParamsInspection */
                $preprocessor->addStorageProcessor($container->get($id));
            }
        }
        foreach ($container->findTaggedServiceIds('preprocessor.annotation') as $id => $attrs) {
            foreach ($attrs as $params) {
                /** @noinspection PhpParamsInspection */
                $preprocessor->addAnnotationProcessor($params['type'], $container->get($id));
            }
        }

        $preprocessor->process($container);
    }
}
