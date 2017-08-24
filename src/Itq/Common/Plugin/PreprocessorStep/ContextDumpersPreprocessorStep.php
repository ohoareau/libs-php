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

use Itq\Common\Aware;
use Itq\Common\Traits;
use Itq\Common\PreprocessorContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ContextDumpersPreprocessorStep extends Base\AbstractPreprocessorStep implements Aware\Plugin\ContextDumperPluginAwareInterface
{
    use Traits\PluginAware\ContextDumperPluginAwareTrait;
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        $ctx->endTime  = microtime(true);
        $ctx->duration = $ctx->endTime - $ctx->startTime;
        $ctx->memory   = memory_get_usage(true) - $ctx->memory;

        $ctx->prepareForSave();

        foreach ($this->getContextDumpers() as $dumper) {
            $dumper->dump($ctx);
        }
    }
}
