<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Plugin;
use Itq\Common\PreprocessorContext;
use Itq\Common\PreprocessableClassFinder;

use Doctrine\Common\Annotations\AnnotationReader;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PreprocessorService
{
    use Traits\ServiceTrait;
    use Traits\AnnotationReaderAwareTrait;
    use Traits\PreprocessableClassFinderAwareTrait;
    /**
     * @param AnnotationReader          $reader
     * @param PreprocessableClassFinder $finder
     */
    public function __construct(AnnotationReader $reader, PreprocessableClassFinder $finder)
    {
        $this->setAnnotationReader($reader);
        $this->setPreprocessableClassFinder($finder);
    }
    /**
     * @param ContainerBuilder $c
     *
     * @return PreprocessorContext
     */
    public function process(ContainerBuilder $c)
    {
        $ctx = new PreprocessorContext(
            [
                'cacheDir'  => $c->getParameter('kernel.cache_dir'),
                'classes'   => $this->getPreprocessableClassFinder()->findClasses($c->getParameter('app_analyzed_dirs')),
            ]
        );

        $this
            ->processErrorMappings($ctx, $c)
            ->processAnnotations($ctx, $c)
            ->processStorages($ctx, $c)
            ->processTags($ctx, $c)
            ->processEvents($ctx, $c)
            ->processConnections($ctx, $c)
            ->processDumpers($ctx, $c)
        ;

        return $ctx;
    }
    /**
     * @param Plugin\ContextDumperInterface $contextDumper
     */
    public function addContextDumper(Plugin\ContextDumperInterface $contextDumper)
    {
        $this->setArrayParameterKey('contextDumpers', uniqid('context-dumper'), $contextDumper);
    }
    /**
     * @param Plugin\TagProcessorInterface $tagProcessor
     */
    public function addTagProcessor(Plugin\TagProcessorInterface $tagProcessor)
    {
        $this->pushArrayParameterKeyItem('tagProcs', $tagProcessor->getTag(), $tagProcessor);
    }
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
     * @param string                              $type
     * @param Plugin\AnnotationProcessorInterface $processor
     */
    public function addAnnotationProcessor($type, Plugin\AnnotationProcessorInterface $processor)
    {
        $this->pushArrayParameterKeyItem(sprintf('%sAnnotProcs', $type), $processor->getAnnotationClass(), $processor);
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return $this
     */
    protected function processErrorMappings(
        /** @noinspection PhpUnusedParameterInspection */ PreprocessorContext $ctx,
        ContainerBuilder $container
    ) {
        $mappings = $container->hasParameter('app_error_mappings') ? $container->getParameter('app_error_mappings') : [];

        if (!is_array($mappings)) {
            $mappings = [];
        }

        foreach ($mappings as $mapping) {
            $container->getDefinition('app.errormanager')->addMethodCall('addKeyCodeMapping', $mapping);
        }

        return $this;
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function processAnnotations(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($ctx->classes as $class) {
            $ctx->class  = $class;
            $ctx->rClass = new \ReflectionClass($class);
            $this->processPreClassAnnotations($ctx, $container);
        }
        unset($ctx->class, $ctx->rClass);
        foreach ($ctx->classes as $class) {
            $ctx->class  = $class;
            $ctx->rClass = new \ReflectionClass($class);
            $this->processClassAnnotations($ctx, $container);
            $this->processClassMethodAnnotations($ctx, $container);
            $this->processClassPropertyAnnotations($ctx, $container);
        }
        unset($ctx->class, $ctx->rClass);

        return $this;
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    protected function processPreClassAnnotations(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($this->getAnnotationReader()->getClassAnnotations($ctx->rClass) as $a) {
            if (!$this->hasArrayParameterKey('preClassAnnotProcs', get_class($a))) {
                continue;
            }
            foreach ($this->getArrayParameterKey('preClassAnnotProcs', get_class($a)) as $processor) {
                /** @var Plugin\AnnotationProcessorInterface $processor */
                $processor->process(get_object_vars($a), $container, $ctx);
            }
        }
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    protected function processClassAnnotations(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($this->getAnnotationReader()->getClassAnnotations($ctx->rClass) as $a) {
            if (!$this->hasArrayParameterKey('classAnnotProcs', get_class($a))) {
                continue;
            }
            foreach ($this->getArrayParameterKey('classAnnotProcs', get_class($a)) as $processor) {
                /** @var Plugin\AnnotationProcessorInterface $processor */
                $processor->process(get_object_vars($a), $container, $ctx);
            }
        }
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    protected function processClassMethodAnnotations(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($ctx->rClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $rMethod) {
            $ctx->rMethod = $rMethod;
            $ctx->method  = $rMethod->getName();
            foreach ($this->getAnnotationReader()->getMethodAnnotations($rMethod) as $a) {
                if (!$this->hasArrayParameterKey('classMethodAnnotProcs', get_class($a))) {
                    continue;
                }
                foreach ($this->getArrayParameterKey('classMethodAnnotProcs', get_class($a)) as $processor) {
                    /** @var Plugin\AnnotationProcessorInterface $processor */
                    $processor->process(get_object_vars($a), $container, $ctx);
                }
            }
        }
        unset($ctx->method, $ctx->rMethod);
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    protected function processClassPropertyAnnotations(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($ctx->rClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $rProperty) {
            $ctx->property  = $rProperty->getName();
            $ctx->rProperty = $rProperty;
            foreach ($this->getAnnotationReader()->getPropertyAnnotations($rProperty) as $a) {
                if (!$this->hasArrayParameterKey('classPropertyAnnotProcs', get_class($a))) {
                    continue;
                }
                foreach ($this->getArrayParameterKey('classPropertyAnnotProcs', get_class($a)) as $processor) {
                    /** @var Plugin\AnnotationProcessorInterface $processor */
                    $processor->process(get_object_vars($a), $container, $ctx);
                }
            }
        }
        unset($ctx->property, $ctx->rProperty);
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function processStorages(
        /** @noinspection PhpUnusedParameterInspection */ PreprocessorContext $ctx,
        ContainerBuilder $container
    ) {
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

        return $this;
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return $this
     */
    protected function processTags(PreprocessorContext $ctx, ContainerBuilder $container)
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

        return $this;
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return $this
     */
    protected function processEvents(
        /** @noinspection PhpUnusedParameterInspection */ PreprocessorContext $ctx,
        ContainerBuilder $container
    ) {
        $ea     = $container->getDefinition('app.event');
        $events = $container->getParameter('app_events');
        foreach ($container->getParameter('app_batchs') as $eventName => $info) {
            $events['batchs_'.$eventName] = $info;
        }
        foreach ($events as $eventName => $info) {
            $eventName = false === strpos($eventName, '.') ? str_replace('_', '.', $eventName) : $eventName;
            $ea->addTag('kernel.event_listener', ['event' => $eventName, 'method' => 'consume']);
            $generalOptions = isset($info['throwExceptions']) ? ['throwException' => $info['throwExceptions']] : [];
            foreach ($info['actions'] as $a) {
                $options = $a;
                unset($options['action'], $options['params']);
                $ea->addMethodCall('register', [$eventName, $a['action'], $a['params'], $options + $generalOptions]);
            }
        }

        return $this;
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function processConnections(
        /** @noinspection PhpUnusedParameterInspection */ PreprocessorContext $ctx,
        ContainerBuilder $container
    ) {
        $connections    = [];
        $connectionBags = [];
        foreach ($container->findTaggedServiceIds('app.connection_bag') as $id => $attributes) {
            foreach ($attributes as $params) {
                $connections[$params['type']]    = [];
                $connectionBags[$params['type']] = $id;
            }
        }
        foreach ($container->getParameter('app_connections') as $type => $connectionsInfos) {
            if (!isset($connections[$type])) {
                throw $this->createRequiredException("Unknown connection type '%s'", $type);
            }
            $connections[$type] = $connectionsInfos + $connections[$type];
        }
        foreach ($connectionBags as $type => $id) {
            $container->getDefinition($id)->replaceArgument(0, $connections[$type]);
        }

        return $this;
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @return $this
     */
    protected function processDumpers(
        PreprocessorContext $ctx,
        /** @noinspection PhpUnusedParameterInspection */ ContainerBuilder $container
    ) {
        $ctx->endTime  = microtime(true);
        $ctx->duration = $ctx->endTime - $ctx->startTime;
        $ctx->memory   = memory_get_usage(true) - $ctx->memory;

        foreach ($this->getArrayParameter('contextDumpers') as $dumper) {
            /** @var Plugin\ContextDumperInterface $dumper */
            $dumper->dump($ctx);
        }

        return $this;
    }
}
