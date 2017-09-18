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

use Exception;
use Itq\Common\PreprocessorContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ConnectionsPreprocessorStep extends Base\AbstractPreprocessorStep
{
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @throws Exception
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        $connections    = [];
        $connectionBags = [];
        foreach ($container->findTaggedServiceIds('app.connection_bag') as $id => $attributes) {
            foreach ($attributes as $params) {
                $connections[$params['type']]    = [];
                $connectionBags[$params['type']] = $id;
            }
        }
        foreach ($container->getParameter('app_connections') as $type => $connectionsInfos) {
            if (!isset($connections[$type])) {
                throw $this->createRequiredException("Unknown connection type '%s'", $type);
            }
            $connections[$type] = $connectionsInfos + $connections[$type];
        }
        foreach ($connectionBags as $type => $id) {
            $d = $container->getDefinition($id);
            $d->replaceArgument(0, $connections[$type]);
        }
    }
}
