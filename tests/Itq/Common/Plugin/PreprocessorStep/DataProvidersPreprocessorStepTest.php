<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\PreprocessorStep;

use Itq\Common\PreprocessorContext;
use Itq\Common\Plugin\DataProvider\ArrayDataProvider;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Itq\Common\Plugin\PreprocessorStep\DataProvidersPreprocessorStep;
use Itq\Common\Tests\Plugin\PreprocessorStep\Base\AbstractPreprocessorStepTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/preprocessor-steps
 * @group plugins/preprocessor-steps/data-provider
 */
class DataProvidersPreprocessorStepTest extends AbstractPreprocessorStepTestCase
{
    /**
     * @return DataProvidersPreprocessorStep
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }

    /**
     * @param array $expectedDefinitions
     * @param array $expectedParams
     * @param array $params
     *
     * @group integ
     *
     * @dataProvider getExecuteData
     */
    public function testExecute($expectedDefinitions, $expectedParams, array $params)
    {
        $c = new ContainerBuilder();
        $ctx = new PreprocessorContext();

        foreach ($params as $k => $v) {
            $c->setParameter($k, $v);
        }

        $this->s()->execute($ctx, $c);

        $defs = $c->getDefinitions();
        unset($defs['service_container']);

        $this->assertEquals($expectedDefinitions, $defs);
        $this->assertEquals($expectedParams, $c->getParameterBag()->all());
    }
    /**
     * @return array
     */
    public function getExecuteData()
    {
        return [
            [[], [], []],
            [
                [],
                ['__itq_data_providers' => null],
                ['__itq_data_providers' => null],
            ],
            [
                [],
                ['__itq_data_providers' => null],
                ['__itq_data_providers' => []],
            ],
            [
                [
                    'itq.dataprovider.generated.'.md5(__FILE__.'.json') => (new Definition(
                        ArrayDataProvider::class,
                        [["a" => 1, "b" => 2, "c" => ["d" => 3, "e" => false], "f" => false, "g" => []]]
                    ))->addTag('app.dataprovider', ['type' => 'data_provider_type']),
                ],
                ['__itq_data_providers' => null],
                ['__itq_data_providers' => [['path' => __FILE__.'.json', 'type' => 'data_provider_type']]],
            ],
            [
                [
                    'itq.dataprovider.generated.'.md5('c@'.__FILE__.'.json') => (new Definition(
                        ArrayDataProvider::class,
                        [["d" => 3, "e" => false]]
                    ))->addTag('app.dataprovider', ['type' => 'data_provider_type']),
                ],
                ['__itq_data_providers' => null],
                ['__itq_data_providers' => [['path' => __FILE__.'.json', 'type' => 'data_provider_type', 'key' => 'c']]],
            ],
        ];
    }
}
