<?php

/*
 * This file is part of the WS package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Bundle\ItqBundle\DependencyInjection\Compiler;

use Itq\Bundle\ItqBundle\DependencyInjection\Compiler\PreprocessorCompilerPass;

use Itq\Common\Service\PreprocessorService;
use Itq\Common\Tests\Base\AbstractTestCase;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <cto@itiqiti.com>
 *
 * @group compiler-passes
 * @group compiler-passes/preprocessor
 */
class PreprocessorCompilerPassTest extends AbstractTestCase
{
    /**
     * @return PreprocessorCompilerPass
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
    /**
     * @group unit
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();

        $container->set('preprocessor.preprocessor', $this->mock('preprocessor', PreprocessorService::class));

        $this->mock('preprocessor')->expects($this->once())->method('process')->with($container)->willReturn(null);

        $this->p()->process($container);
    }
}
