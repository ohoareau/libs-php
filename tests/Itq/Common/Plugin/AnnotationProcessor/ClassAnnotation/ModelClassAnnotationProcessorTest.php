<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\AnnotationProcessor\ClassAnnotation;

use Itq\Common\Plugin\AnnotationProcessor\ClassAnnotation\ModelClassAnnotationProcessor;
use Itq\Common\Tests\Plugin\AnnotationProcessor\ClassAnnotation\Base\AbstractClassAnnotationProcessorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/annotation-processors
 * @group plugins/annotation-processors/classes
 * @group plugins/annotation-processors/classes/model
 */
class ModelClassAnnotationProcessorTest extends AbstractClassAnnotationProcessorTestCase
{
    /**
     * @return ModelClassAnnotationProcessor
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
