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
class GroupTypeTagProcessor extends Base\AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.group_type';
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
     */
    public function process($tag, array $params, $id, Definition $d, ContainerBuilder $container, $ctx)
    {
        $type = $params['type'];

        unset($params['name'], $params['type']);

        $this->addCall($container, 'app.group', 'addGroupType', [$type, new Reference($id), $params]);

        if (!isset($ctx->enums['groupTypes'])) {
            $ctx->enums['groupTypes'] = [];
        }

        $ctx->enums['groupTypes'][$type] = true;
    }
}
