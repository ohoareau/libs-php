<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\Base;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $this->registerCompilerPasses($container);

        $this->postBuild($container);
    }
    /**
     * @param ContainerBuilder $container
     */
    protected function registerCompilerPasses(ContainerBuilder $container)
    {
        $beforePasses = $this->getRegistrableBeforeCompilerPasses();

        if (0 < count($beforePasses)) {
            $config = $container->getCompilerPassConfig();
            $config->setBeforeOptimizationPasses(array_merge($beforePasses, $config->getBeforeOptimizationPasses()));
        }

        foreach ($this->getRegistrableCompilerPasses() as $compilerPass) {
            $container->addCompilerPass($compilerPass);
        }
    }
    /**
     * @return CompilerPassInterface[]
     */
    protected function getRegistrableBeforeCompilerPasses()
    {
        return [];
    }
    /**
     * @return CompilerPassInterface[]
     */
    protected function getRegistrableCompilerPasses()
    {
        return [];
    }
    /**
     * @param ContainerBuilder $container
     */
    protected function postBuild(ContainerBuilder $container)
    {
        $securityListenerFactory = $this->getSecurityListenerFactory();

        if ((null !== $securityListenerFactory) && $container->hasExtension('security')) {
            $extension = $container->getExtension('security');
            /** @var SecurityExtension $extension */
            /** @noinspection PhpParamsInspection */
            $extension->addSecurityListenerFactory($securityListenerFactory);
        }
    }
    /**
     * @return mixed
     */
    protected function getSecurityListenerFactory()
    {
        return null;
    }
}
