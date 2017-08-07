<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle;

use Itq\Bundle\ItqBundle\DependencyInjection\Compiler\PreprocessorCompilerPass;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ItqBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PreprocessorCompilerPass());
    }
}
