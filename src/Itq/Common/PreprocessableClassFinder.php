<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

use Itq\Common\Traits;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use Doctrine\Common\Annotations\AnnotationReader;

use ReflectionClass;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PreprocessableClassFinder
{
    use Traits\ServiceTrait;
    use Traits\AnnotationReaderAwareTrait;
    /**
     * @param AnnotationReader $annotationReader
     */
    public function __construct(AnnotationReader $annotationReader)
    {
        $this->setAnnotationReader($annotationReader);
    }
    /**
     * @param array $dirs
     *
     * @return array
     */
    public function findClasses(array $dirs)
    {
        $classes = [];

        foreach ($dirs as $dir) {
            $classes = array_merge($classes, $this->findCompilerAnnotatedClassesInDirectory($dir));
        }

        return $classes;
    }
    /**
     * @param string $directory
     *
     * @return array
     */
    public function findCompilerAnnotatedClassesInDirectory($directory)
    {
        $f = new Finder();
        $f->files()->in($directory)->name('*.php')->contains("\\Annotation")->notPath('Tests')->contains('class');
        $classes = [];
        foreach ($f as $file) {
            $matches = null;
            $ns = null;
            /** @var SplFileInfo $file */
            $content = $file->getContents();
            if (0 < preg_match('/namespace\s+([^\s;]+)\s*;/', $content, $matches)) {
                $ns = $matches[1].'\\';
            }
            if (0 < preg_match_all('/^\s*class\s+([^\s\:]+)\s+/m', $content, $matches)) {
                /** @noinspection PhpIncludeInspection */
                require_once $file->getRealPath();
                foreach ($matches[1] as $class) {
                    $fullClass = $ns.$class;
                    if (!$this->isCompilerAnnotatedClass($fullClass)) {
                        continue;
                    }
                    $classes[$fullClass] = true;
                }
            }
        }
        $classes = array_keys($classes);
        sort($classes);

        return $classes;
    }
    /**
     * @param string $class
     *
     * @return bool
     */
    public function isCompilerAnnotatedClass($class)
    {
        $rClass = new ReflectionClass($class);
        foreach ($this->getAnnotationReader()->getClassAnnotations($rClass) as $a) {
            if ($a instanceof AnnotationInterface) {
                return true;
            }
        }
        foreach ($rClass->getMethods() as $rMethod) {
            foreach ($this->getAnnotationReader()->getMethodAnnotations($rMethod) as $a) {
                if ($a instanceof AnnotationInterface) {
                    return true;
                }
            }
        }
        foreach ($rClass->getProperties() as $rProperty) {
            foreach ($this->getAnnotationReader()->getPropertyAnnotations($rProperty) as $a) {
                if ($a instanceof AnnotationInterface) {
                    return true;
                }
            }
        }

        return false;
    }
}
