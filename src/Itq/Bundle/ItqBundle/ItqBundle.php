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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ItqBundle extends Base\AbstractBundle
{
    /**
     * @return CompilerPassInterface[]
     */
    protected function getRegistrableCompilerPasses()
    {
        return [
            new PreprocessorCompilerPass(),
        ];
    }
}
