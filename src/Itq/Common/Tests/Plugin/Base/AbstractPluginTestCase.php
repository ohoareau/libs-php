<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\Base;

use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractPluginTestCase extends AbstractTestCase
{
    /**
     * @return AbstractPlugin
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
}
