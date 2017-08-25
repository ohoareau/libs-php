<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\TestMock;

use PHPUnit_Framework_MockObject_MockObject;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait AnnotationReaderTestMockTrait
{
    /**
     * @param string            $name
     * @param null|string|mixed $class
     * @param null|array        $methods
     *
     * @return mixed|PHPUnit_Framework_MockObject_MockObject
     */
    abstract protected function mocked($name, $class = null, $methods = null);
    /**
     * @return AnnotationReader|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedAnnotationReader()
    {
        return $this->mocked('annotationReader', AnnotationReader::class);
    }
}
