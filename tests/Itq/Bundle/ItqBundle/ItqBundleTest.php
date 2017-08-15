<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Bundle\ItqBundle;

use Itq\Bundle\ItqBundle\DependencyInjection\Compiler\FirstCompilerPass;
use Itq\Bundle\ItqBundle\DependencyInjection\Compiler\PreprocessorCompilerPass;
use Itq\Common\Tests\Base\AbstractBundleTestCase;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group bundles
 * @group bundles/itq
 */
class ItqBundleTest extends AbstractBundleTestCase
{
    /**
     * @group integ
     */
    public function testBuild()
    {
        $container = new ContainerBuilder();

        $this->b()->build($container);

        $passes = $container->getCompilerPassConfig()->getBeforeOptimizationPasses();

        $this->assertEquals(FirstCompilerPass::class, get_class(current($passes)));
        $this->assertEquals(PreprocessorCompilerPass::class, get_class(end($passes)));
    }
}
