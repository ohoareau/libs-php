<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\AnnotationProcessor\ClassMethodAnnotation;

use Itq\Common\Annotation;
use Itq\Common\PreprocessorContext;
use Itq\Common\Plugin\AnnotationProcessor\Base\AbstractAnnotationProcessor;

use Itq\Common\Traits as CommonTraits;

use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SdkClassMethodAnnotationProcessor extends AbstractAnnotationProcessor
{
    use CommonTraits\AnnotationReaderAwareTrait;
    /**
     * @param AnnotationReader $annotationReader
     */
    public function __construct(AnnotationReader $annotationReader)
    {
        $this->setAnnotationReader($annotationReader);
    }
    /**
     * @return string
     */
    public function getAnnotationClass()
    {
        return Annotation\Sdk::class;
    }
    /**
     * @param array               $params
     * @param ContainerBuilder    $container
     * @param PreprocessorContext $ctx
     *
     * @throws \Exception
     */
    public function process($params, ContainerBuilder $container, PreprocessorContext $ctx)
    {
        /** @var Route $routeAnnotation */
        $routeAnnotation = $this->getAnnotationReader()->getMethodAnnotation($ctx->rMethod, Route::class);
        if (null === $routeAnnotation) {
            throw $this->createRequiredException('Sdk annotation require route annotation in controller');
        }
        /** @var Annotation\ResponseModel $responseModelAnnotation */
        $responseModelAnnotation = $this->getAnnotationReader()->getMethodAnnotation($ctx->rMethod, Annotation\ResponseModel::class);
        $responseType = (null !== $responseModelAnnotation) ?
            ['mode' => 'model', 'type' => $responseModelAnnotation->type, 'group' => $responseModelAnnotation->group, 'collection' => $responseModelAnnotation->collection] :
            ['mode' => 'value', 'value' => null]
        ;
        $target    = $params['target'];
        $service   = $params['service'];
        $method    = $params['method'];
        $type      = $params['type'];
        $subParams = $params['params'];
        unset($params['target'], $params['service'], $params['method'], $params['type'], $params['params'], $params['value']);

        $ctx->addSdkMethod($target, $ctx->class, $ctx->method, $routeAnnotation->getPath(), $service, $method, $type, $subParams, $responseType, $params);
    }
}
