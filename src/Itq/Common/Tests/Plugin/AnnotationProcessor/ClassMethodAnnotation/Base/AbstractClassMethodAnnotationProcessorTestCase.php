<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\AnnotationProcessor\ClassMethodAnnotation\Base;

use Itq\Common\Plugin\AnnotationProcessorInterface;
use Itq\Common\Tests\Plugin\AnnotationProcessor\Base\AbstractAnnotationProcessorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractClassMethodAnnotationProcessorTestCase extends AbstractAnnotationProcessorTestCase
{
    /**
     * @return AnnotationProcessorInterface
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
