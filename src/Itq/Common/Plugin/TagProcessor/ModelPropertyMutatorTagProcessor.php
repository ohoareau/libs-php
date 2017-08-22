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

use Itq\Common\PreprocessorContext;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelPropertyMutatorTagProcessor extends Base\AbstractTagProcessor
{
    /**
     * @return string
     */
    public function getTag()
    {
        return 'app.modelpropertymutator';
    }
    /**
     * @param string              $tag
     * @param array               $params
     * @param string              $id
     * @param Definition          $d
     * @param ContainerBuilder    $container
     * @param PreprocessorContext $ctx
     *
     * @return void
     *
     * @throws \Exception
     */
    public function process($tag, array $params, $id, Definition $d, ContainerBuilder $container, $ctx)
    {
        $this->registerServicePlugin($tag, $id, $params, 'app.model', 'propertyMutator', $ctx);
    }
}
