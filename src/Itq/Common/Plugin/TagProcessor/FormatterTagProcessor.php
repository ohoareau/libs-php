<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\TagProcessor;

use Itq\Common\Traits;
use Itq\Common\Annotation;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Doctrine\Common\Annotations\AnnotationReader;

use ReflectionClass;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FormatterTagProcessor extends Base\AbstractTagProcessor
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
     * @return string
     */
    public function getTag()
    {
        return 'app.formatter';
    }
    /**
     * @param string           $tag
     * @param array            $params
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     * @param object           $ctx
     *
     * @return void
     *
     * @throws \Exception
     */
    public function process($tag, array $params, $id, Definition $d, ContainerBuilder $container, $ctx)
    {
        /** @var ReflectionClass $rClass */
        $rClass = $ctx->rClass;
        foreach ($rClass->getMethods(\ReflectionProperty::IS_PUBLIC) as $rMethod) {
            foreach ($this->getAnnotationReader()->getMethodAnnotations($rMethod) as $a) {
                if ($a instanceof Annotation\Formatter) {
                    $vars = get_object_vars($a);
                    $type = $vars['value'];
                    unset($vars['value']);
                    $this->addCall($container, 'app.formatter', 'register', [$type, [new Reference($id), $rMethod->getName()], $vars]);
                }
            }
        }
    }
}
