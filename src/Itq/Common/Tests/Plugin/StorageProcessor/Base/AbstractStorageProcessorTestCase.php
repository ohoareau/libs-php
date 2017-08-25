<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\StorageProcessor\Base;

use Itq\Common\Plugin\StorageProcessorInterface;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractStorageProcessorTestCase extends AbstractPluginTestCase
{
    /**
     * @return StorageProcessorInterface
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
