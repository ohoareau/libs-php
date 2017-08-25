<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Formatter;

use Itq\Common\Plugin\Formatter\XmlFormatter;
use Itq\Common\Tests\Plugin\Formatter\Base\AbstractFormatterTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/formatters
 * @group plugins/formatters/xml
 */
class XmlFormatterTest extends AbstractFormatterTestCase
{
    /**
     * @return XmlFormatter
     */
    public function f()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::f();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedSerializer()];
    }
}
