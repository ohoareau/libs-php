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
class StoragesPreprocessorStep extends Base\AbstractPreprocessorStep
{
    /**
     * @param Plugin\StorageProcessorInterface $processor
     */
    public function addStorageProcessor(Plugin\StorageProcessorInterface $processor)
    {
        foreach (is_array($processor->getType()) ? $processor->getType() : [$processor->getType()] as $type) {
            $this->setArrayParameterKey('storageProcs', $type, $processor);
        }
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return void
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($container->getParameter('app_storages') as $storageName => $storage) {
            /** @var Plugin\StorageProcessorInterface $processor */
            $storage   = (is_array($storage) ? ($storage) : []) + ['mount' => '/', 'type' => 'file'];
            $processor = $this->getArrayParameterKey('storageProcs', $storage['type']);
            $mount     = $storage['mount'];
            unset($storage['type'], $storage['mount']);
            $definition = $processor->build($storage);
            $definition->addTag('app.storage', ['name' => $storageName, 'mount' => $mount]);
            $container->setDefinition(sprintf('app.generated.storages.%s', $storageName), $definition);
        }
    }
}
