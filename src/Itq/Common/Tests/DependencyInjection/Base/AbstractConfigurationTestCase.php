<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\DependencyInjection\Base;

use Itq\Common\Tests\Base\AbstractTestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractConfigurationTestCase extends AbstractTestCase
{
    /**
     * @return ConfigurationInterface
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
}
