<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Plugin;
use Itq\Common\Service\RequestService;
use Symfony\Component\HttpFoundation\Request;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/request
 */
class RequestServiceTest extends AbstractServiceTestCase
{
    /**
     * @return RequestService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @param mixed   $expected
     * @param Request $request
     *
     * @group unit
     *
     * @dataProvider getFetchQueryLimitData
     */
    public function testFetchQueryLimit($expected, Request $request)
    {
        $this->assertEquals($expected, $this->s()->fetchQueryLimit($request));
    }
    /**
     * @return array
     */
    public function getFetchQueryLimitData()
    {
        return [
            [1, new Request(['limit' => 1])],
            [10, new Request(['limit' => 10])],
            [10, new Request(['limit' => 10.4])],
            [10, new Request(['limit' => '10'])],
            [10, new Request(['limit' => '010'])],
            [null, new Request(['limit' => null])],
            [null, new Request(['limit' => ''])],
            [null, new Request()],
        ];
    }
    /**
     * @param mixed   $expected
     * @param Request $request
     *
     * @group unit
     *
     * @dataProvider getFetchQueryCriteriaData
     */
    public function testFetchQueryCriteria($expected, Request $request)
    {
        $this->assertEquals($expected, $this->s()->fetchQueryCriteria($request));
    }
    /**
     * @return array
     */
    public function getFetchQueryCriteriaData()
    {
        return [
            [['a' => 'b'], new Request(['criteria' => ['a' => 'b']])],
            [[], new Request(['criteria' => null])],
            [[], new Request(['criteria' => ''])],
            [[], new Request(['criteria' => true])],
            [[], new Request(['criteria' => 1])],
            [[], new Request(['criteria' => -1])],
            [[], new Request(['criteria' => 0])],
            [[], new Request(['criteria' => false])],
            [[], new Request()],
        ];
    }
    /**
     * @param string $type
     * @param string $pluginClass
     * @param array  $methods
     * @param string $getter
     * @param string $adder
     * @param string $optionalTypeForAdder
     * @param string $optionalSingleGetter
     *
     * @group unit
     *
     * @dataProvider getPluginsData
     */
    public function testPlugins($type, $pluginClass, array $methods, $getter, $adder, $optionalTypeForAdder = null, $optionalSingleGetter = null)
    {
        $this->handleTestPlugins($type, $pluginClass, $methods, $getter, $adder, $optionalTypeForAdder, $optionalSingleGetter);
    }
    /**
     * @return array
     */
    public function getPluginsData()
    {
        return [
            ['codec', Plugin\RequestCodecInterface::class, ['encode', 'decode'], 'getCodecs', 'addCodec', 'thecodec', 'getCodec'],
        ];
    }
    /**
     * @group unit
     */
    public function testParse()
    {
        $r = new Request();

        $this->s()->addCodec(
            'thecodec',
            new Plugin\RequestCodec\ValueRequestCodec(
                function () {
                    return func_get_args();
                }
            )
        );

        $this->assertEquals(
            ['thecodec' => [$r, ['theOption' => 'theValue']]],
            $this->s()->parse($r, ['thecodec'], ['theOption' => 'theValue'])
        );

        $this->assertEquals(
            ['thecodec' => [$r, ['theOption2' => 'theValue2']]],
            $this->s()->parse($r, [], ['theOption2' => 'theValue2'])
        );
    }
}
