<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\DependencyInjection\Compiler\Base;

use Itq\Common\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractPreprocessorCompilerPass extends AbstractCompilerPass
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /** @var Service\PreprocessorService $preprocessor */
        $preprocessor = $container->get('preprocessor.preprocessor');

        $this->processPreprocessor($preprocessor, $container);
    }
    /**
     * @param ContainerBuilder $container
     * @param string           $tag
     *
     * @return array
     */
    protected function findServiceTags(ContainerBuilder $container, $tag)
    {
        $serviceTags = [];

        foreach ($container->findTaggedServiceIds($tag) as $id => $attrs) {
            foreach ($attrs as $params) {
                $serviceTags[] = ['serviceId' => $id, 'params' => $params, 'service' => $container->get($id)];
            }
        }

        return $serviceTags;
    }
    /**
     * @param Service\PreprocessorService $preprocessorService
     * @param ContainerBuilder            $container
     *
     * @return void
     */
    abstract protected function processPreprocessor(Service\PreprocessorService $preprocessorService, ContainerBuilder $container);
}
