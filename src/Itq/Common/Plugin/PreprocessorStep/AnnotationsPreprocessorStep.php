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

use Itq\Common\Traits;
use Itq\Common\Plugin;
use Itq\Common\PreprocessorContext;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AnnotationsPreprocessorStep extends Base\AbstractPreprocessorStep
{
    use Traits\AnnotationReaderAwareTrait;
    /**
     * @param AnnotationReader $annotationReader
     */
    public function __construct(AnnotationReader $annotationReader)
    {
        $this->setAnnotationReader($annotationReader);
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
     * @return void
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
}
