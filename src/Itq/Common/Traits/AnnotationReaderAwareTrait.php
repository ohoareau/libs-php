<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * AnnotationReaderAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait AnnotationReaderAwareTrait
{
    /**
     * @param string $key
     * @param mixed  $service
     *
     * @return $this
     */
    protected abstract function setService($key, $service);
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getService($key);
    /**
     * @param AnnotationReader $annotationReader
     *
     * @return $this
     */
    public function setAnnotationReader(AnnotationReader $annotationReader = null)
    {
        return $this->setService('annotationReader', $annotationReader);
    }
    /**
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this->getService('annotationReader');
    }
}
