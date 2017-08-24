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
class StoragesPreprocessorStep extends Base\AbstractPreprocessorStep implements Aware\StorageProcessorPluginAwareInterface
{
    use Traits\PluginAware\StorageProcessorPluginAwareTrait;
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($container->getParameter('app_storages') as $storageName => $storage) {
            $storage   = (is_array($storage) ? ($storage) : []) + ['mount' => '/', 'type' => 'file'];
            $processor = $this->getStorageProcessor($storage['type']);
            $mount     = $storage['mount'];
            unset($storage['type'], $storage['mount']);
            $definition = $processor->build($storage);
            $definition->addTag('app.storage', ['name' => $storageName, 'mount' => $mount]);
            $container->setDefinition(sprintf('app.generated.storages.%s', $storageName), $definition);
        }
    }
}
