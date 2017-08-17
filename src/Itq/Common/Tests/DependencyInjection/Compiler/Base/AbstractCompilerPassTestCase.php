<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\DependencyInjection\Compiler\Base;

use Itq\Common\Tests\Base\AbstractTestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractCompilerPassTestCase extends AbstractTestCase
{
    /**
     * @return CompilerPassInterface
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
}
