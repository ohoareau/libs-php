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

use Itq\Common\Plugin;
use Itq\Common\PreprocessorContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TagsPreprocessorStep extends Base\AbstractPreprocessorStep
{
    /**
     * @param Plugin\TagProcessorInterface $tagProcessor
     */
    public function addTagProcessor(Plugin\TagProcessorInterface $tagProcessor)
    {
        $this->pushArrayParameterKeyItem('tagProcs', $tagProcessor->getTag(), $tagProcessor);
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return void
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($this->getArrayParameter('tagProcs') as $tag => $processors) {
            /** @var Plugin\TagProcessorInterface $processor */
            foreach ($container->findTaggedServiceIds($tag) as $id => $attributes) {
                $d = $container->getDefinition($id);
                $ctx->rClass = new \ReflectionClass($d->getClass());
                foreach ($processors as $processor) {
                    $processor->preprocess($tag, $id, $d, $container, $ctx);
                    foreach ($attributes as $params) {
                        $processor->process($tag, $params, $id, $d, $container, $ctx);
                    }
                }
                unset($ctx->rClass);
            }
        }
    }
}
