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

use ReflectionClass;
use Itq\Common\Traits;
use Itq\Common\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TaskTagProcessor extends Base\AbstractTagProcessor
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
        return 'app.task';
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
     */
    public function process($tag, array $params, $id, Definition $d, ContainerBuilder $container, $ctx)
    {
        /** @var ReflectionClass $rClass */
        $rClass = $ctx->rClass;
        foreach ($rClass->getMethods(\ReflectionProperty::IS_PUBLIC) as $rMethod) {
            foreach ($this->getAnnotationReader()->getMethodAnnotations($rMethod) as $a) {
                if ($a instanceof Annotation\Task) {
                    $vars   = get_object_vars($a);
                    $name   = $vars['value'];
                    unset($vars['value']);
                    $this->addCall($container, 'app.task', 'register', [$name, [new Reference($id), $rMethod->getName()], $vars]);
                }
            }
        }
    }
}
