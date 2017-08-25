<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\PreprocessorStep;

use Itq\Common\PreprocessorContext;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Itq\Common\Plugin\PreprocessorStep\AnnotationsPreprocessorStep;
use Itq\Common\Tests\Plugin\PreprocessorStep\Base\AbstractPreprocessorStepTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/preprocessor-steps
 * @group plugins/preprocessor-steps/annotations
 */
class AnnotationsPreprocessorStepTest extends AbstractPreprocessorStepTestCase
{
    /**
     * @return AnnotationsPreprocessorStep
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [new AnnotationReader()];
    }
    /**
     * @group unit
     */
    public function testExecute()
    {
        $cb   = new ContainerBuilder();
        $ctx  = new PreprocessorContext();

        $ctx->class = 'test';
        $this->assertEquals('test', $ctx->class);
        $this->s()->execute($ctx, $cb);
        $this->assertFalse(isset($ctx->class));
    }
}
