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
use Itq\Common\Plugin\ModelPropertyLinearizer\Base\AbstractModelPropertyLinearizer;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;
use Itq\Common\Service\Model\ModelPropertyLinearizerService;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services/model
 * @group services/model/property-linearizer
 */
class ModelPropertyLinearizerServiceTest extends AbstractServiceTestCase
{
    /**
     * @return ModelPropertyLinearizerService
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
            ['propertyLinearizer', Plugin\ModelPropertyLinearizerInterface::class, ['supports', 'linearize'], 'getModelPropertyLinearizers', 'addModelPropertyLinearizer'],
        ];
    }
    /**
     * @group unit
     */
    public function testlinearizeWithDocNotAObjectThrowException()
    {
        $this->expectExceptionThrown($this->createMalformedException('Not a valid object'));
        $this->s()->linearize('its not an object');
    }
    /**
     * @group unit
     */
    public function testlinearize()
    {
        $doc = (object) [
            'data' => 'a value',
            'dataWithLinearizer' => 'a value to linearize',
            'dataIsNull' => null,
            'dataCleared' => '*cleared*',
        ];
        $data = (array) $doc;
        $meta = ['metadate'];
        $dummyLinearizer = $this->getMockForAbstractClass(AbstractModelPropertyLinearizer::class);
        /** @var AbstractModelPropertyLinearizer $dummyLinearizer */
        $this->s()->addModelPropertyLinearizer($dummyLinearizer);

        $this->mockMethod($this->mockedMetaDataService(), 'getModel', [$doc], $meta);
        /** @var PHPUnit_Framework_MockObject_MockObject $dummyLinearizer */
        $this->mockMethod(
            $dummyLinearizer,
            'supports',
            [$data, 'data', 'a value', $meta],
            false,
            $this->at(0)
        );
        $this->mockMethod(
            $dummyLinearizer,
            'supports',
            [$data, 'dataWithLinearizer', 'a value to linearize', $meta],
            true,
            $this->at(1)
        );
        $this->mockMethod(
            $dummyLinearizer,
            'linearize',
            [$data, 'dataWithLinearizer', 'a value to linearize', $meta],
            $this->returnCallback(function (&$data) {
                $data['dataWithLinearizer'] = 'linearized data';

                return $data['dataWithLinearizer'];
            })
        );
        $actual = $this->s()->linearize($doc);
        $this->assertEqualsResultSet(compact('doc', 'actual'));
    }
}
