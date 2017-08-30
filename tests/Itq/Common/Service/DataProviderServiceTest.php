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
use Itq\Common\Service\DataProviderService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/data-provider
 */
class DataProviderServiceTest extends AbstractServiceTestCase
{
    /**
     * @return DataProviderService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @param string $type
     * @param string $pluginClass
     * @param array  $methods
     * @param string $getter
     * @param string $adder
     * @param string $optionalTypeForAdder
     * @param string $optionalSingleGetter
     * @param string $optionalGroupGetter
     *
     * @group unit
     *
     * @dataProvider getPluginsData
     */
    public function testPlugins($type, $pluginClass, array $methods, $getter, $adder, $optionalTypeForAdder = null, $optionalSingleGetter = null, $optionalGroupGetter = null)
    {
        $this->handleTestPlugins($type, $pluginClass, $methods, $getter, $adder, $optionalTypeForAdder, $optionalSingleGetter, $optionalGroupGetter);
    }
    /**
     * @return array
     */
    public function getPluginsData()
    {
        return [
            ['dataProvider', Plugin\DataProviderInterface::class, ['provide'], 'getDataProviders', 'addDataProvider', 'theprovider', null, 'getDataProvidersByType'],
        ];
    }
    /**
     * @param mixed  $expected
     * @param string $type
     * @param array  $providers
     * @param array  $options
     *
     * @group integ
     *
     * @dataProvider getProvideData
     */
    public function testProvide($expected, $type, array $providers, array $options = [])
    {
        foreach ($providers as $providerType => $typeProviders) {
            foreach ($typeProviders as $provider) {
                $this->s()->addDataProvider($providerType, $provider);
            }
        }

        $this->assertEquals($expected, $this->s()->provide($type, $options));
    }
    /**
     * @return array
     */
    public function getProvideData()
    {
        return [
            '0 - no dataproviders' => [[], 'someType', []],
            '1 - one data provider only' => [
                ['a' => 1, 'b' => 2],
                'declaredType',
                [
                    'declaredType' => [
                        new Plugin\DataProvider\ValueDataProvider(['a' => 1, 'b' => 2]),
                    ],
                ],
            ],
            '2 - type is passed to dataprovider options' => [
                ['type' => 'thegiventype', 'option1' => 1, 'option2' => 2],
                'thegiventype',
                [
                    'thegiventype' => [
                        new Plugin\DataProvider\ValueDataProvider(
                            function (array $options) {
                                return ['option1' => 1, 'option2' => 2] + $options;
                            }
                        ),
                    ],
                ],
            ],
            '3 - multiple dataproviders data are merged (no overlap)' => [
                ['a' => 1, 'b' => 2],
                'declaredType',
                [
                    'declaredType' => [
                        new Plugin\DataProvider\ValueDataProvider(['a' => 1]),
                        new Plugin\DataProvider\ValueDataProvider(['b' => 2]),
                    ],
                ],
            ],
            '4 - multiple datproviders data are merged (with overlap and deep merge)' => [
                ['a' => ['b' => 1, 'c' => 2], 'd' => 12, 'e' => 14],
                'declaredType',
                [
                    'declaredType' => [
                        new Plugin\DataProvider\ValueDataProvider(['a' => ['b' => 1], 'd' => 12]),
                        new Plugin\DataProvider\ValueDataProvider(['a' => ['c' => 2], 'e' => 14]),
                    ],
                ],
            ],
        ];
    }
}
