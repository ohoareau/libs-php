<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Adapter\Base;

use Itq\Common\Tests\Base\AbstractTestCase;
use Itq\Common\Adapter\Php\Base\AbstractPhpAdapter;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractAdapterTestCase extends AbstractTestCase
{
    /**
     * @return AbstractPhpAdapter
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
}
