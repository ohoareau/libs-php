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
use Itq\Common\Plugin\ContextDumperInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Itq\Common\Plugin\PreprocessorStep\ContextDumpersPreprocessorStep;
use Itq\Common\Tests\Plugin\PreprocessorStep\Base\AbstractPreprocessorStepTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/preprocessor-steps
 * @group plugins/preprocessor-steps/context-dumpers
 */
class ContextDumpersPreprocessorStepTest extends AbstractPreprocessorStepTestCase
{
    /**
     * @return ContextDumpersPreprocessorStep
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testExecute()
    {
        $cd1 = $this->mocked('contextDumper1', ContextDumperInterface::class, ['dump']);
        $cd2 = $this->mocked('contextDumper2', ContextDumperInterface::class, ['dump']);
        $cb  = new ContainerBuilder();
        $ctx = new PreprocessorContext(['a' => 1]);

        $cd1->expects($this->once())->method('dump')->with($ctx)->willReturnCallback(
            function ($ctx) {
                $ctx->a = 2;
            }
        );
        $cd2->expects($this->once())->method('dump')->with($ctx)->willReturnCallback(
            function ($ctx) {
                $ctx->a = 3;
            }
        );

        $this->s()->addContextDumper($cd1);
        $this->s()->addContextDumper($cd2);

        $this->assertEquals(1, $ctx->a);
        $this->s()->execute($ctx, $cb);
        $this->assertEquals(3, $ctx->a);
    }
}
