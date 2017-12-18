<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service\Model;

use Itq\Common\Plugin;
use Itq\Common\Service\Model\ModelDynamicPropertyBuilderService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;
use Tests\Itq\Common\Model\Model1;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services/model
 * @group services/model/dynamic-property-builder
 */
class ModelDynamicPropertyBuilderServiceTest extends AbstractServiceTestCase
{
    /**
     * @return ModelDynamicPropertyBuilderService
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
        return [$this->mockedMetaDataService()];
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
            ['dynamicPropertyBuilder', Plugin\ModelDynamicPropertyBuilderInterface::class, ['supports', 'build'], 'getModelDynamicPropertyBuilders', 'addModelDynamicPropertyBuilder'],
        ];
    }
    /**
     * @group unit
     */
    public function testBuildProperty()
    {
        $subDoc = new Model1();
        $subDoc->property = null;
        $doc = (object) ['subModel' => $subDoc];

        $requestedField = 'subModel.property';
        $computeRequestedSubField = 'property';
        $ctx = (object) ['models' => []];
        $modelDefinition = ['definition'];
        $builtValue = 'built value';

        $mockedPlugin = $this->mockedModelDynamicPropertyBuilderPlugin();
        $this->s()->addModelDynamicPropertyBuilder($mockedPlugin);

        $this->mockedMetaDataService()->expects($this->once())->method('isModel')->with($subDoc)->willReturn(true);
        $this->mockedMetaDataService()->expects($this->once())->method('getModelIdForClass')->with($subDoc)->willReturn('model1');
        $this->mockedMetaDataService()->expects($this->once())->method('fetchModelDefinition')->with(get_class($subDoc))->willReturn($modelDefinition);

        $mockedPlugin->expects($this->once())->method('supports')->with($subDoc, $computeRequestedSubField, $modelDefinition)->willReturn(true);
        $mockedPlugin->expects($this->once())->method('build')->with($subDoc, $computeRequestedSubField, $modelDefinition)->willReturn($builtValue);

        $this->s()->buildProperty('unknownmodel', $doc, $requestedField, $ctx);

        $this->assertEquals($builtValue, $doc->subModel->property);
    }
    /**
     * @return Plugin\ModelDynamicPropertyBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedModelDynamicPropertyBuilderPlugin()
    {
        return $this->getMockBuilder(Plugin\ModelDynamicPropertyBuilderInterface::class)->getMockForAbstractClass();
    }
}
