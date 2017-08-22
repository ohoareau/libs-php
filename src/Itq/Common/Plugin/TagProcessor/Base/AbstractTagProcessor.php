<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\TagProcessor\Base;

use Exception;
use Itq\Common\PreprocessorContext;
use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\TagProcessorInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTagProcessor extends AbstractPlugin implements TagProcessorInterface
{
    /**
     * @param string           $tag
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     * @param object           $ctx
     */
    public function preprocess($tag, $id, Definition $d, ContainerBuilder $container, $ctx)
    {
    }
    /**
     * @param ContainerBuilder $container
     * @param string           $id
     * @param string           $method
     * @param array            $params
     *
     * @return $this
     */
    protected function addCall(ContainerBuilder $container, $id, $method, array $params = [])
    {
        $container->getDefinition($id)->addMethodCall($method, $params);

        return $this;
    }
    /**
     * @param string               $tag
     * @param string               $id
     * @param array                $params
     * @param string               $serviceId
     * @param string               $type
     * @param PreprocessorContext  $ctx
     * @param array                $options
     *
     * @throws Exception
     */
    protected function registerServicePlugin($tag, $id, array $params, $serviceId, $type, $ctx, array $options = [])
    {
        $options    += ['adderMethod' => sprintf('add%s', ucfirst($type))];
        $adderMethod = $options['adderMethod'];

        unset($tag);

        $ctx->registerContainerMethodCall($serviceId, $adderMethod, [new Reference($id)], $params);
    }
    /**
     * @param string               $tag
     * @param string               $id
     * @param array                $params
     * @param string               $serviceId
     * @param string               $type
     * @param PreprocessorContext  $ctx
     * @param array                $options
     *
     * @throws Exception
     */
    protected function registerNamedServicePlugin($tag, $id, array $params, $serviceId, $type, $ctx, array $options = [])
    {
        $options    += ['typeKey' => 'type', 'adderMethod' => sprintf('add%s', ucfirst($type))];
        $typeKey     = $options['typeKey'];
        $adderMethod = $options['adderMethod'];

        if (!isset($params[$typeKey])) {
            throw $this->createRequiredException("Missing '%s' for %s service '%s' (tagged with '%s')", $typeKey, $type, $id, $tag);
        }

        $ctx->registerContainerMethodCall($serviceId, $adderMethod, [$params[$typeKey], new Reference($id)], $params);
    }
}
