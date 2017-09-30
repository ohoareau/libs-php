<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Adapter\Php\Base;

use Itq\Common\Adapter\Php\Base\AbstractPhpAdapter;
use Itq\Common\Tests\Adapter\Base\AbstractAdapterTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractPhpAdapterTestCase extends AbstractAdapterTestCase
{
    /**
     * @return AbstractPhpAdapter
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
}
