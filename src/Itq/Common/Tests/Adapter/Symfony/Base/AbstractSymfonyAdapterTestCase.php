<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Adapter\Symfony\Base;

use Itq\Common\Tests\Adapter\Base\AbstractAdapterTestCase;
use Itq\Common\Adapter\Symfony\Base\AbstractSymfonyAdapter;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractSymfonyAdapterTestCase extends AbstractAdapterTestCase
{
    /**
     * @return AbstractSymfonyAdapter
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
}
