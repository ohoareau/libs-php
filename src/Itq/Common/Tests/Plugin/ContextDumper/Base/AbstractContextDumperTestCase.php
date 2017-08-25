<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\ContextDumper\Base;

use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;
use Itq\Common\Plugin\ContextDumper\Base\AbstractContextDumper;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractContextDumperTestCase extends AbstractPluginTestCase
{
    /**
     * @return AbstractContextDumper
     */
    public function d()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
}
