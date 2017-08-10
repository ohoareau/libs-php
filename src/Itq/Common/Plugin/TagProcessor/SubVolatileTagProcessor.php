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

use Itq\Common\Plugin\TagProcessor\Base\AbstractTagProcessor;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SubVolatileTagProcessor extends AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.volatile.sub';
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
     */
    public function process($tag, array $params, $id, Definition $d, ContainerBuilder $container, $ctx)
    {
        list($type, $subType) = array_slice(explode('.', $id), -3);
        $params += ['id' => $type.'.'.$subType];

        $ctx->crudServiceIds[strtolower($params['id'])] = $id;

        $d->addMethodCall('setTypes', [[$type, $subType]]);
        $d->addMethodCall('setFormService', [new Reference('app.form')]);
        $d->addMethodCall('setMetaDataService', [new Reference('app.metadata')]);
        $d->addMethodCall('setModelService', [new Reference('app.model')]);
        $d->addMethodCall('setBusinessRuleService', [new Reference('app.businessRule')]);
        $d->addMethodCall('setWorkflowService', [new Reference('app.workflow')]);
        $d->addMethodCall('setLogger', [new Reference('logger')]);
        $d->addMethodCall('setEventDispatcher', [new Reference('event_dispatcher')]);
        $d->addMethodCall('setErrorManager', [new Reference('app.errormanager')]);
    }
}
