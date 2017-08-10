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
class ProviderAccountTagProcessor extends AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.provider.account';
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
        $this->addCall(
            $container,
            'app.userprovider',
            'setAccountProvider',
            [
                new Reference($id),
                isset($params['type']) ? $params['type'] : 'default',
                isset($params['method']) ? $params['method'] : 'get',
                isset($params['format']) ? $params['format'] : 'plain',
                isset($params['authentified']) ? true === $params['authentified'] : false,
                isset($params['usernameKeys']) ? explode(',', $params['usernameKeys']) : ['username', 'id'],
            ]
        );
    }
}
