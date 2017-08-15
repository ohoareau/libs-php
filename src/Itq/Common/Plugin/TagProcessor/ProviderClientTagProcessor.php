<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\TagProcessor;

use Itq\Common\Service\DecoratedClientService;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ProviderClientTagProcessor extends Base\AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.provider.client';
    }
    /**
     * @param string           $tag
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     * @param object           $ctx
     *
     * @return void
     *
     * @throws \Exception
     */
    public function process($tag, array $params, $id, Definition $d, ContainerBuilder $container, $ctx)
    {
        if (!$container->hasDefinition('app.security.authentication.provider')) {
            return ;
        }

        if ((isset($params['method']) && 'get' !== $params['method']) || isset($params['format'])) {
            $ref = new Definition(
                DecoratedClientService::class,
                [
                    new Reference($id),
                    isset($params['method']) ? $params['method'] : 'get',
                    isset($params['format']) ? $params['format'] : 'raw',
                ]
            );

            $id = sprintf('app.client_%s', md5(uniqid()));
            $container->setDefinition($id, $ref);
        }

        $this->addCall($container, 'app.security.authentication.provider', 'setClientProvider', [new Reference($id)]);
        $this->addCall($container, 'app.request', 'setClientProvider', [new Reference($id)]);
    }
}
