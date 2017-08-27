<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PreprocessorStep;

use Itq\Common\PreprocessorContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RegisteredContainerMethodCallsPreprocessorStep extends Base\AbstractPreprocessorStep
{
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($ctx->getRegisteredContainerMethodCalls() as $serviceIdentification => $methodCalls) {
            $services = [$serviceIdentification];
            if ('#' === $serviceIdentification{0}) {
                $services = $this->getTaggedServiceIds(substr($serviceIdentification, 1), $container);
            }

            foreach ($services as $serviceId) {
                foreach ($methodCalls as $methodName => $calls) {
                    $unprioritorizedCalls = [];
                    foreach ($calls as $i => $call) {
                        if (!isset($call[1]) || !is_array($call[1]) || !isset($call[1]['priority'])) {
                            unset($call[1]);
                            $unprioritorizedCalls[] = $call;
                            unset($calls[$i]);
                        }
                    }
                    usort(
                        $calls,
                        function ($a, $b) {
                            return ($a[1]['priority'] > $b[1]['priority']) ? -1 : ($a[1]['priority'] === $b[1]['priority'] ? 0 : 1);
                        }
                    );
                    foreach (array_merge(array_values($calls), $unprioritorizedCalls) as $call) {
                        $container->getDefinition($serviceId)->addMethodCall($methodName, $call[0]);
                    }
                }
            }
        }
    }
    /**
     * @param string           $tag
     * @param ContainerBuilder $container
     *
     * @return string[]
     */
    protected function getTaggedServiceIds($tag, ContainerBuilder $container)
    {
        return array_keys($container->findTaggedServiceIds($tag));
    }
}
