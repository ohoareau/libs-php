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

use ReflectionClass;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MigratorTagProcessor extends Base\AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.migrator';
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
        /** @var ReflectionClass $rClass */
        $rClass = $ctx->rClass;
        if ($rClass->isSubclassOf(ContainerAwareInterface::class)) {
            $d->addMethodCall('setContainer', [new Reference('service_container')]);
        }
        if ($rClass->isSubclassOf(LoggerAwareInterface::class)) {
            $d->addMethodCall('setLogger', [new Reference('logger')]);
        }

        $this->addCall($container, 'app.migration', 'addMigrator', [new Reference($id), $params['extension']]);
    }
}
