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
use Itq\Common\PreprocessorContext;
use Itq\Common\Service\MetaDataService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/metadata
 */
class MetaDataServiceTest extends AbstractServiceTestCase
{
    /**
     * @return MetaDataService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [new PreprocessorContext()];
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
            ['descriptor', Plugin\ModelDescriptorInterface::class, ['describe'], 'getModelDescriptors', 'addModelDescriptor', 'thedescriptor', 'getModelDescriptor'],
        ];
    }
    /**
     * @param mixed $expected
     * @param mixed $class
     * @param array $registeredModels
     * @param array $registeredModelDescriptors
     *
     * @group unit
     *
     * @dataProvider getGetModelDescriptionData
     */
    public function testGetModelDescription($expected, $class, array $registeredModels = [], array $registeredModelDescriptors = [])
    {
        if ($expected instanceof \Exception) {
            $this->expectExceptionThrown($expected);
        }

        foreach ($registeredModels as $modelId => $modelDefinition) {
            $this->s()->getPreprocessorContext()->addModel($modelId, $modelDefinition);
        }

        foreach ($registeredModelDescriptors as $type => $modelDescriptor) {
            $this->s()->addModelDescriptor($type, $modelDescriptor);
        }

        $result = $this->s()->getModelDescription($class);

        if (!($expected instanceof \Exception)) {
            $this->assertArraySubset($expected, $result);
        }
    }
    /**
     * @return array
     */
    public function getGetModelDescriptionData()
    {
        return [
            '0 - unknown model'  => [new \RuntimeException("Class 'TheClass' is not registered as a model", 500), 'TheClass'],
            '1 - no descriptors' => [['id' => 'theClass', 'class' => 'TheClass'], 'TheClass', ['TheClass' => ['id' => 'theClass']]],
            '2 - one descriptor' => [
                ['id' => 'theClass', 'class' => 'TheClass', 'key1' => ['subKey1' => 'value1']],
                'TheClass',
                ['TheClass' => ['id' => 'theClass']],
                ['d1' => new Plugin\ModelDescriptor\MemoryModelDescriptor(['key1' => ['subKey1' => 'value1']])],
            ],
            '3 - two descriptors (no overlap)' => [
                ['id' => 'theClass', 'class' => 'TheClass', 'key1' => ['subKey1' => 'value1'], 'key2' => 'value2'],
                'TheClass',
                ['TheClass' => ['id' => 'theClass']],
                [
                    'd1' => new Plugin\ModelDescriptor\MemoryModelDescriptor(['key1' => ['subKey1' => 'value1']]),
                    'd2' => new Plugin\ModelDescriptor\MemoryModelDescriptor(['key2' => 'value2']),
                ],
            ],
            '4 - two descriptors (overlap)' => [
                ['id' => 'theClass', 'class' => 'TheClass', 'key1' => ['subKey1' => 'value1', 'subKey2' => 'value1'], 'key2' => 'value2', 'key3' => 'value1'],
                'TheClass',
                ['TheClass' => ['id' => 'theClass']],
                [
                    'd1' => new Plugin\ModelDescriptor\MemoryModelDescriptor(['key1' => ['subKey1' => 'value1', 'subKey2' => 'value1'], 'key3' => 'value1']),
                    'd2' => new Plugin\ModelDescriptor\MemoryModelDescriptor(['key1' => ['subKey1' => 'value2'], 'key2' => 'value2']),
                ],
            ],
        ];
    }
}
