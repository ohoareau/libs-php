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
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AnnotationsPreprocessorStep extends Base\AbstractPreprocessorStep implements Aware\Plugin\AnnotationProcessorPluginAwareInterface
{
    use Traits\AnnotationReaderAwareTrait;
    use Traits\PluginAware\AnnotationProcessorPluginAwareTrait;
    /**
     * @param AnnotationReader $annotationReader
     */
    public function __construct(AnnotationReader $annotationReader)
    {
        $this->setAnnotationReader($annotationReader);
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
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
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    protected function processPreClassAnnotations(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($this->getAnnotationReader()->getClassAnnotations($ctx->rClass) as $a) {
            $this->executeProcessors('preClass', $a, $container, $ctx);
        }
    }
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     */
    protected function processClassAnnotations(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        foreach ($this->getAnnotationReader()->getClassAnnotations($ctx->rClass) as $a) {
            $this->executeProcessors('class', $a, $container, $ctx);
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
                $this->executeProcessors('classMethod', $a, $container, $ctx);
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
                $this->executeProcessors('classProperty', $a, $container, $ctx);
            }
        }
        unset($ctx->property, $ctx->rProperty);
    }
    /**
     * @param string              $type
     * @param object              $a
     * @param ContainerBuilder    $c
     * @param PreprocessorContext $ctx
     */
    protected function executeProcessors($type, $a, ContainerBuilder $c, PreprocessorContext $ctx)
    {
        foreach ($this->getAnnotationProcessorsForClass($type, get_class($a)) as $processor) {
            $processor->process(get_object_vars($a), $c, $ctx);
        }
    }
}
