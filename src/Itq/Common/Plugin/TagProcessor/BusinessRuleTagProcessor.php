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
use Itq\Common\Plugin\TagProcessor\Base\AbstractTagProcessor;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Doctrine\Common\Annotations\AnnotationReader;

use ReflectionClass;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BusinessRuleTagProcessor extends AbstractTagProcessor
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
        return 'app.business_rule';
    }
    /**
     * @param string           $tag
     * @param string           $id
     * @param Definition       $d
     * @param ContainerBuilder $container
     * @param object           $ctx
     */
    public function preprocess($tag, $id, Definition $d, ContainerBuilder $container, $ctx)
    {
        $d->addMethodCall('setErrorManager', [new Reference('app.errormanager')]);
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
                if ($a instanceof Annotation\BusinessRule) {
                    $vars       = get_object_vars($a);
                    $method     = $rMethod->getName();
                    $vars['id'] = isset($vars['id']) ? $vars['id'] : (isset($vars['value']) ? $vars['value'] : null);
                    $brId       = strtoupper($vars['id']);
                    $brName     = strtolower(isset($vars['name']) ? $vars['name'] : trim(join(' ', preg_split('/(?=\\p{Lu})/', ucfirst($method)))));

                    unset($vars['value'], $vars['id'], $vars['name']);

                    $this->addCall($container, 'app.businessRule', 'register', [$brId, $brName, [new Reference($id), $method], $vars]);
                }
            }
        }
    }
}
