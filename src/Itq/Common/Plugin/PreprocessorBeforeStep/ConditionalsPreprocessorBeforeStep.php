<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PreprocessorBeforeStep;

use Itq\Common\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ConditionalsPreprocessorBeforeStep extends Base\AbstractPreprocessorBeforeStep
{
    /**
     * @param Plugin\ConditionalBeforeProcessorInterface $beforeProcessor
     */
    public function addConditionalBeforeProcessor(Plugin\ConditionalBeforeProcessorInterface $beforeProcessor)
    {
        foreach (is_array($beforeProcessor->getCondition()) ? $beforeProcessor->getCondition() : [$beforeProcessor->getCondition()] as $condition) {
            $this->setArrayParameterKey('conditionalBeforeProcs', $condition, $beforeProcessor);
        }
    }
    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function execute(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('app.conditioned') as $id => $attributes) {
            $d = $container->getDefinition($id);
            $scope = null;
            foreach ($attributes as $params) {
                if (isset($params['condition'])) {
                    /** @var Plugin\ConditionalBeforeProcessorInterface $processor */
                    $processor = $this->getArrayParameterKey('conditionalBeforeProcs', $params['condition']);
                    if (!$processor->isKept($params, $id, $d, $container, $params['condition'])) {
                        $container->removeDefinition($id);
                        break;
                    }
                } else {
                    $scope = 'tag';
                }
            }
            if ('tag' === $scope) {
                $tags    = $d->getTags();
                $updated = false;
                foreach ($tags as $tagName => $tagEntries) {
                    if ('app.conditioned' === $tagName) {
                        continue;
                    }
                    foreach ($tagEntries as $i => $params) {
                        if (isset($params['condition'])) {
                            /** @var Plugin\ConditionalBeforeProcessorInterface $processor */
                            $processor = $this->getArrayParameterKey('conditionalBeforeProcs', $params['condition']);
                            if (!$processor->isKept($params, $id, $d, $container, $params['condition'])) {
                                $updated = true;
                                unset($tags[$tagName][$i]);
                            }
                        }
                    }
                }
                if ($updated) {
                    $d->setTags($tags);
                }
            }
        }
    }
}
