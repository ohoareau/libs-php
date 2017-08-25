<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\AnnotationProcessor\ClassPropertyAnnotation;

use Itq\Common\Plugin\AnnotationProcessor\ClassPropertyAnnotation\DefaultValueClassPropertyAnnotationProcessor;
use Itq\Common\Tests\Plugin\AnnotationProcessor\ClassPropertyAnnotation\Base\AbstractClassPropertyAnnotationProcessorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/annotation-processors
 * @group plugins/annotation-processors/class-properties
 * @group plugins/annotation-processors/class-properties/default-value
 */
class DefaultValueClassPropertyAnnotationProcessorTest extends AbstractClassPropertyAnnotationProcessorTestCase
{
    /**
     * @return DefaultValueClassPropertyAnnotationProcessor
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
