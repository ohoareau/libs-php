<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Adapter\Php;

use Itq\Common\Adapter\Php\DecoratedNativePhpAdapter;
use Itq\Common\Tests\Adapter\Php\Base\AbstractPhpAdapterTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group adapters
 * @group adapters/php
 * @group adapters/php/decorated-native
 */
class DecoratedNativePhpAdapterTest extends AbstractPhpAdapterTestCase
{
    /**
     * @return DecoratedNativePhpAdapter
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::a();
    }
    /**
     * @param mixed  $expected
     * @param string $key
     * @param array  $decorateds
     *
     * @group integ
     * @dataProvider getGetDefinedConstantsData
     */
    public function testGetDefinedConstants($expected, $key, array $decorateds = [])
    {
        foreach ($decorateds as $key => $value) {
            $this->a()->setDecoratedConstant($key, $value);
        }

        $this->assertEquals($expected, $this->a()->getDefinedConstant($key));
    }
    /**
     * @return array
     */
    public function getGetDefinedConstantsData()
    {
        return [
            [PHP_OS, 'PHP_OS'],
            ['TheMockedValue', 'PHP_OS', ['PHP_OS' => 'TheMockedValue']],
            ['TheMockedValue#2', 'PHP_OS', ['PHP_OS' => 'TheMockedValue#2']],
        ];
    }
    /**
     * @param mixed  $expected
     * @param string $key
     * @param array  $decorateds
     *
     * @group integ
     * @dataProvider getIsDefinedConstantsData
     */
    public function testIsDefinedConstants($expected, $key, array $decorateds = [])
    {
        foreach ($decorateds as $key => $value) {
            $this->a()->setDecoratedConstant($key, $value);
        }

        $this->assertEquals($expected, $this->a()->isDefinedConstant($key));
    }
    /**
     * @return array
     */
    public function getIsDefinedConstantsData()
    {
        return [
            [true, 'PHP_OS'],
            [true, 'PHP_OS', ['PHP_OS' => 'TheMockedValue']],
            [false, 'UNKNOWN_CONSTANT'],
            [true, 'UNKNOWN_CONSTANT', ['UNKNOWN_CONSTANT' => 'TheValue']],
        ];
    }
}
