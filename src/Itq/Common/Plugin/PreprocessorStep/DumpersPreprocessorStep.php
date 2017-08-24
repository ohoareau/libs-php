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
class DumpersPreprocessorStep extends Base\AbstractPreprocessorStep
{
    /**
     * @param Plugin\ContextDumperInterface $contextDumper
     */
    public function addContextDumper(Plugin\ContextDumperInterface $contextDumper)
    {
        $this->setArrayParameterKey('contextDumpers', uniqid('context-dumper'), $contextDumper);
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return void
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        $ctx->endTime  = microtime(true);
        $ctx->duration = $ctx->endTime - $ctx->startTime;
        $ctx->memory   = memory_get_usage(true) - $ctx->memory;

        $ctx->prepareForSave();

        foreach ($this->getArrayParameter('contextDumpers') as $dumper) {
            /** @var Plugin\ContextDumperInterface $dumper */
            $dumper->dump($ctx);
        }
    }
}
