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
     * @param string                           $name
     * @param Plugin\PreprocessorStepInterface $step
     *
     * @return $this
     */
    public function addStep($name, Plugin\PreprocessorStepInterface $step)
    {
        return $this->setArrayParameterKey('steps', $name, $step);
    }
    /**
     * @param string                                 $name
     * @param Plugin\PreprocessorBeforeStepInterface $step
     *
     * @return $this
     */
    public function addBeforeStep($name, Plugin\PreprocessorBeforeStepInterface $step)
    {
        return $this->setArrayParameterKey('beforeSteps', $name, $step);
    }
    /**
     * @return Plugin\PreprocessorStepInterface[]
     */
    public function getSteps()
    {
        return $this->getArrayParameter('steps');
    }
    /**
     * @return Plugin\PreprocessorBeforeStepInterface[]
     */
    public function getBeforeSteps()
    {
        return $this->getArrayParameter('beforeSteps');
    }
    /**
     * @param ContainerBuilder $c
     */
    public function beforeProcess(ContainerBuilder $c)
    {
        foreach ($this->getBeforeSteps() as $step) {
            $step->execute($c);
        }
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
                'cacheDir' => $c->getParameter('kernel.cache_dir'),
                'classes'  => $this->getPreprocessableClassFinder()->findClasses($c->getParameter('app_analyzed_dirs')),
            ]
        );

        foreach ($this->getSteps() as $step) {
            $step->execute($ctx, $c);
        }

        return $ctx;
    }
}
