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
class ProviderTokenTagProcessor extends Base\AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.provider.token';
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
        $type       = isset($params['type']) ? $params['type'] : 'default';
        $method     = isset($params['method']) ? $params['method'] : 'get';
        $forcedData = json_decode(isset($params['data']) ? $params['data'] : '[]', true);

        if (!is_array($forcedData)) {
            throw $this->createMalformedException("Forced data must be a valid json string for service '%s'", $id);
        }

        $this->addCall($container, 'app.tokenprovider', 'addGenerator', [new Reference($id), $type, $method, $forcedData]);
    }
}
