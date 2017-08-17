<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\DependencyInjection\Security\Factory;

use Itq\Common\Traits;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GenericSecurityFactory implements SecurityFactoryInterface
{
    use Traits\ServiceTrait;
    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->setParameter('key', $key);
    }
    /**
     * @param ContainerBuilder $container
     * @param string           $id
     * @param mixed            $config
     * @param string           $userProvider
     * @param mixed            $defaultEntryPoint
     *
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $key        = $this->getParameter('key');
        $providerId = sprintf('app.security.authentication.provider.%s.%s', $key, $id);
        $listenerId = sprintf('app.security.authentication.listener.%s.%s', $key, $id);

        $container->setDefinition($providerId, new DefinitionDecorator('app.security.authentication.provider'))->replaceArgument(0, new Reference($userProvider));
        $container->setDefinition($listenerId, new DefinitionDecorator('app.security.authentication.listener'));

        return [$providerId, $listenerId, $defaultEntryPoint];
    }
    /**
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }
    /**
     * @return string
     */
    public function getKey()
    {
        return $this->getParameter('key');
    }
    /**
     * @param NodeDefinition $node
     *
     * @return void
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
