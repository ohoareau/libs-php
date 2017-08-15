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
class PartnerTagProcessor extends Base\AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.partner';
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
        if (!isset($params['type'])) {
            throw $this->createRequiredException("Missing type for partner service '%s'", $id);
        }
        if (!isset($params['id'])) {
            throw $this->createRequiredException("Missing id for partner service '%s'", $id);
        }

        $partnerType = $params['type'];
        $partnerId   = $params['id'];

        unset($params['type'], $params['id']);

        $this->addCall($container, 'app.partner', 'register', [$partnerType, $partnerId, new Reference($id), $params]);
    }
}
