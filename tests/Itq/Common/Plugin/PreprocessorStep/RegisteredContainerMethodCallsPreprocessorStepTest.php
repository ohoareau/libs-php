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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Itq\Common\Tests\Plugin\PreprocessorStep\Base\AbstractPreprocessorStepTestCase;
use Itq\Common\Plugin\PreprocessorStep\RegisteredContainerMethodCallsPreprocessorStep;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/preprocessor-steps
 * @group plugins/preprocessor-steps/registered-container-method-calls
 */
class RegisteredContainerMethodCallsPreprocessorStepTest extends AbstractPreprocessorStepTestCase
{
    /**
     * @return RegisteredContainerMethodCallsPreprocessorStep
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
        $cb   = new ContainerBuilder();
        $ctx  = new PreprocessorContext();

        $this->s()->execute($ctx, $cb);
        $this->assertTrue(true);
    }
}
