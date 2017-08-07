<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\AnnotationProcessor\ClassPropertyAnnotation;

use Itq\Common\Annotation;
use Itq\Common\PreprocessorContext;
use Itq\Common\Plugin\AnnotationProcessor\Base\AbstractAnnotationProcessor;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class EmbeddedReferenceClassPropertyAnnotationProcessor extends AbstractAnnotationProcessor
{
    /**
     * @return string
     */
    public function getAnnotationClass()
    {
        return Annotation\EmbeddedReference::class;
    }
    /**
     * @param array               $params
     * @param ContainerBuilder    $container
     * @param PreprocessorContext $ctx
     */
    public function process($params, ContainerBuilder $container, PreprocessorContext $ctx)
    {
        $ctx->addModelPropertyEmbeddedReference($ctx->class, $ctx->property, $params);
    }
}
