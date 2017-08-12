<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ConditionalBeforeProcessor\Base;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class HasPredefinedItemsConditionalBeforeProcessor extends AbstractConditionalBeforeProcessor
{
    /**
     * @return array
     */
    public function getCondition()
    {
        return ['has_batchs', 'has_businessrules', 'has_db_connections', 'has_googledrive', 'has_sdks'];
    }
    /**
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     * @param string           $condition
     *
     * @return bool
     */
    public function isKept(array $params, $id, Definition $d, ContainerBuilder $container, $condition)
    {
        $method = sprintf('test%s', str_replace(' ', '', ucwords(str_replace('_', ' ', $condition))));

        return $this->$method($params, $id, $d, $container);
    }
    /**
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    protected function testHasBatchs(
        /** @noinspection PhpUnusedParameterInspection */ array $params,
        $id,
        Definition $d,
        ContainerBuilder $container
    ) {
        return $container->hasParameter('app_batchs') && 0 < count($container->getParameter('app_batchs'));
    }
    /**
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    protected function testHasBusinessrules(
        /** @noinspection PhpUnusedParameterInspection */ array $params,
        $id,
        Definition $d,
        ContainerBuilder $container
    ) {
        return 0 < count($container->findTaggedServiceIds('app.businessrule'));
    }
    /**
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    protected function testHasDbConnections(
        /** @noinspection PhpUnusedParameterInspection */ array $params,
        $id,
        Definition $d,
        ContainerBuilder $container
    ) {
        return $container->hasParameter('app_connections') && 0 < count($container->getParameter('app_connections'));
    }
    /**
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    protected function testHasGoogledrive(
        /** @noinspection PhpUnusedParameterInspection */ array $params,
        $id,
        Definition $d,
        ContainerBuilder $container
    ) {
        return true;
    }
    /**
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    protected function testHasSdks(
        /** @noinspection PhpUnusedParameterInspection */ array $params,
        $id,
        Definition $d,
        ContainerBuilder $container
    ) {
        return 0 < count($container->findTaggedServiceIds('app.sdk_generator'));
    }
}
