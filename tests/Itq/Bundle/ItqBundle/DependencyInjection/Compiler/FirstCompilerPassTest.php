<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Bundle\ItqBundle\DependencyInjection\Compiler;

use Itq\Common\Service\PreprocessorService;
use Itq\Common\Tests\DependencyInjection\Compiler\Base\AbstractCompilerPassTestCase;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group compiler-passes
 * @group compiler-passes/first
 */
class FirstCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @group unit
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();

        $container->set('preprocessor.preprocessor', $this->mock('preprocessor', PreprocessorService::class));

        $this->mock('preprocessor')->expects($this->once())->method('beforeProcess')->with($container)->willReturn(null);

        $this->p()->process($container);
    }
}
