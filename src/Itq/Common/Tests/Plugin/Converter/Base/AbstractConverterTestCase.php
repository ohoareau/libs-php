<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\Converter\Base;

use Itq\Common\Plugin\Converter\Base\AbstractConverter;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractConverterTestCase extends AbstractPluginTestCase
{
    /**
     * @return AbstractConverter
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->p();
    }
}
