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

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RepositoryTagProcessor extends Base\AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.repository';
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
        $typeName     = substr($id, strrpos($id, '.') + 1);
        $options      = [];
        $params       += ['id' => $typeName];
        $params['db'] = 'app.database.'.$params['type'];

        foreach ($params as $k => $v) {
            if ('connection' === substr($k, 0, 10)) {
                $options[$k] = $v;
            }
        }

        $ctx->repositoryIds[strtolower($params['id'])] = $id;

        $d->addMethodCall('setCollectionName', [isset($params['collection']) ? $params['collection'] : $typeName]);
        $d->addMethodCall('setConnectionOptions', [$options]);
        $d->addMethodCall('setLogger', [new Reference('logger')]);
        $d->addMethodCall('setDatabaseService', [new Reference($params['db'])]);
        $d->addMethodCall('setErrorManager', [new Reference('app.errormanager')]);
        $d->addMethodCall('setTranslator', [new Reference('translator')]);
        $d->addMethodCall('setEventDispatcher', [new Reference('event_dispatcher')]);
    }
}
