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

use Exception;
use Itq\Common\Traits;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
     * @param string $providerTemplateId
     * @param string $listenerTemplateId
     */
    public function __construct($key, $providerTemplateId, $listenerTemplateId)
    {
        $this->setParameter('key', $key);
        $this->setParameter('providerTemplateId', $providerTemplateId);
        $this->setParameter('listenerTemplateId', $listenerTemplateId);
    }
    /**
     * @param ContainerBuilder $container
     * @param string           $id
     * @param mixed            $config
     * @param string           $userProvider
     * @param mixed            $defaultEntryPoint
     *
     * @return array
     *
     * @throws Exception
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerTemplateId = $this->getParameter('providerTemplateId');
        $listenerTemplateId = $this->getParameter('listenerTemplateId');
        $key                = $this->getParameter('key');
        $providerId         = sprintf('%s.%s.%s', $providerTemplateId, $key, $id);
        $listenerId         = sprintf('%s.%s.%s', $listenerTemplateId, $key, $id);
        $found              = null;
        $tries              = [
            'Symfony\\Component\\DependencyInjection\\ChildDefinition',      // SF >= 3.3
            'Symfony\\Component\\DependencyInjection\\DefinitionDefinition', // SF < 3.3
        ];

        foreach ($tries as $class) {
            if (!class_exists($class)) {
                continue;
            }
            $container->setDefinition($providerId, new $class($providerTemplateId));
            $container->setDefinition($listenerId, new $class($listenerTemplateId));
            $found = $class;
            break;
        }

        if (null === $found) {
            throw $this->createRequiredException(
                'None of classes %s are available to create security factory.',
                join(', ', $tries)
            );
        }

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
