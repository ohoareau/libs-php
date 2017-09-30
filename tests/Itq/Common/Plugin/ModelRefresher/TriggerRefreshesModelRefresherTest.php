<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelRefresher;

use Itq\Common\Model\Value;
use Itq\Common\Plugin\ModelRefresher\TriggerRefreshesModelRefresher;
use Itq\Common\Tests\Plugin\ModelRefresher\Base\AbstractModelRefresherTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/refreshers
 * @group plugins/models/refreshers/trigger-refreshes
 */
class TriggerRefreshesModelRefresherTest extends AbstractModelRefresherTestCase
{
    /**
     * @return TriggerRefreshesModelRefresher
     */
    public function r()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::r();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedMetaDataService(), $this->mockedDateService()];
    }
    /**
     * @group unit
     */
    public function testRefreshForDateTimeType()
    {
        $doc         = new Value();
        $doc->value  = null;
        $options     = ['operation' => 'create'];
        $nowDateTime = new \DateTime('2017-09-30T15:34:00+02:00');

        $this->mockedMetaDataService()->expects($this->once())->method('getModelRefreshablePropertiesByOperation')->with($doc, $options['operation'])->willReturn(['value']);
        $this->mockedMetaDataService()->expects($this->once())->method('getModelPropertyType')->with($doc, 'value')->willReturn(['type' => "DateTime<'c'>"]);
        $this->mockedDateService()->expects($this->once())->method('getCurrentDate')->willReturn($nowDateTime);

        $this->assertEquals(null, $doc->value);
        $this->r()->refresh($doc, $options);
        $this->assertEquals($nowDateTime, $doc->value);
    }
    /**
     * @group unit
     */
    public function testRefreshForUnknownType()
    {
        $doc         = new Value();
        $doc->value  = null;
        $options     = ['operation' => 'create'];

        $this->mockedMetaDataService()->expects($this->once())->method('getModelRefreshablePropertiesByOperation')->with($doc, $options['operation'])->willReturn(['value']);
        $this->mockedMetaDataService()->expects($this->once())->method('getModelPropertyType')->with($doc, 'value')->willReturn(['type' => "unknown_type"]);

        $this->expectExceptionThrown(new \RuntimeException("Unable to refresh model property 'value': unsupported type 'unknown_type'", 500));
        $this->assertEquals(null, $doc->value);
        $this->r()->refresh($doc, $options);
    }
    /**
     * @group unit
     */
    public function testRefreshForNotAnObject()
    {
        $doc        = 'notanobject';
        $options    = ['operation' => 'create'];

        $this->mockedMetaDataService()->expects($this->never())->method('getModelRefreshablePropertiesByOperation');

        $this->r()->refresh($doc, $options);
    }
    /**
     * @group unit
     */
    public function testRefreshForNotAValidOperation()
    {
        $doc        = new Value();
        $doc->value = null;
        $options    = [];

        $this->mockedMetaDataService()->expects($this->never())->method('getModelRefreshablePropertiesByOperation');

        $this->assertEquals(null, $doc->value);
        $this->r()->refresh($doc, $options);
        $this->assertEquals(null, $doc->value);
    }
}
