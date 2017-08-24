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

use Itq\Common\Aware;
use Itq\Common\Traits;
use Itq\Common\PreprocessorContext;
use Itq\Common\PreprocessableClassFinder;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PreprocessorService implements Aware\PreprocessorStepPluginAwareInterface, Aware\PreprocessorBeforeStepPluginAwareInterface
{
    use Traits\ServiceTrait;
    use Traits\AnnotationReaderAwareTrait;
    use Traits\PreprocessableClassFinderAwareTrait;
    use Traits\PluginAware\PreprocessorStepPluginAwareTrait;
    use Traits\PluginAware\PreprocessorBeforeStepPluginAwareTrait;
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
     */
    public function beforeProcess(ContainerBuilder $c)
    {
        foreach ($this->getPreprocessorBeforeSteps() as $step) {
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

        foreach ($this->getPreprocessorSteps() as $step) {
            $step->execute($ctx, $c);
        }

        return $ctx;
    }
}
