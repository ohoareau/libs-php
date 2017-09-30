<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Adapter\Filesystem\Base;

use Itq\Common\Tests\Adapter\Base\AbstractAdapterTestCase;
use Itq\Common\Adapter\Filesystem\Base\AbstractFilesystemAdapter;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractFilesystemAdapterTestCase extends AbstractAdapterTestCase
{
    /**
     * @return AbstractFilesystemAdapter
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
}
