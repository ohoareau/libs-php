<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Converter;

use Exception;
use Itq\Common\Plugin\Converter\DefaultConverter;
use Itq\Common\Tests\Plugin\Converter\Base\AbstractConverterTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/converters
 * @group plugins/converters/default
 */
class DefaultConverterTest extends AbstractConverterTestCase
{
    /**
     * @return DefaultConverter
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @param mixed  $expected
     * @param string $method
     * @param mixed  $value
     *
     * @group unit
     *
     * @dataProvider getConvertData
     */
    public function testConvert($expected, $method, $value)
    {
        if ($expected instanceof Exception) {
            $this->expectExceptionThrown($expected);
        }

        $result = $this->c()->$method($value);

        if (!($expected instanceof Exception)) {
            $this->assertEquals($expected, $result);
        }
    }
    /**
     * @return array
     */
    public function getConvertData()
    {
        return [
            [base64_encode('abcde'), 'convertPlainToBase64', 'abcde'],
            ['abcde', 'convertBase64ToPlain', base64_encode('abcde')],
        ];
    }
}
