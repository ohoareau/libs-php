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
use Itq\Common\Plugin\ModelRefresher\DefaultValuesLoaderModelRefresher;
use Itq\Common\Tests\Plugin\ModelRefresher\Base\AbstractModelRefresherTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/refreshers
 * @group plugins/models/refreshers/default-values-loader
 */
class DefaultValuesLoaderModelRefresherTest extends AbstractModelRefresherTestCase
{
    /**
     * @return DefaultValuesLoaderModelRefresher
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
        return [$this->mockedMetaDataService(), $this->mockedTenantService(), $this->mockedGeneratorService(), $this->mockedDateService()];
    }
    /**
     * @group unit
     */
    public function testRefreshForNowDateTimeAsDefaultValue()
    {
        $doc         = new Value();
        $doc->value  = null;
        $options     = ['operation' => 'create'];
        $nowDateTime = new \DateTime('2017-09-30T15:34:00+02:00');

        $this->mockedMetaDataService()->expects($this->once())->method('getModelDefaults')->with($doc)->willReturn(['value' => ['value' => '{{now}}']]);
        $this->mockedDateService()->expects($this->once())->method('getCurrentDate')->willReturn($nowDateTime);

        $this->assertEquals(null, $doc->value);
        $this->r()->refresh($doc, $options);
        $this->assertEquals($nowDateTime, $doc->value);
    }
    /**
     * @group unit
     */
    public function testRefreshForCurrentTenantAsDefaultValue()
    {
        $doc        = new Value();
        $doc->value = null;
        $options    = ['operation' => 'create'];

        $this->mockedMetaDataService()->expects($this->once())->method('getModelDefaults')->with($doc)->willReturn(['value' => ['value' => '{{tenant}}']]);
        $this->mockedTenantService()->expects($this->once())->method('getCurrent')->willReturn('thetenant');

        $this->assertEquals(null, $doc->value);
        $this->r()->refresh($doc, $options);
        $this->assertEquals('thetenant', $doc->value);
    }
    /**
     * @group unit
     */
    public function testRefreshForAnotherPropertyAsDefaultValue()
    {
        $doc        = new Value();
        $doc->id    = 'theidvalue';
        $doc->value = null;
        $options    = ['operation' => 'create'];

        $this->mockedMetaDataService()->expects($this->once())->method('getModelDefaults')->with($doc)->willReturn(['value' => ['value' => '{{.id}}']]);

        $this->assertEquals(null, $doc->value);
        $this->r()->refresh($doc, $options);
        $this->assertEquals('theidvalue', $doc->value);
    }
    /**
     * @group unit
     */
    public function testRefreshForNotAnObject()
    {
        $doc        = 'notanobject';
        $options    = ['operation' => 'create'];

        $this->mockedMetaDataService()->expects($this->never())->method('getModelDefaults');

        $this->r()->refresh($doc, $options);
    }
    /**
     * @group unit
     */
    public function testRefreshForNotAValidOperation()
    {
        $doc        = new Value();
        $doc->value = null;
        $options    = ['operation' => 'not-valid-operation'];

        $this->mockedMetaDataService()->expects($this->never())->method('getModelDefaults');

        $this->assertEquals(null, $doc->value);
        $this->r()->refresh($doc, $options);
        $this->assertEquals(null, $doc->value);
    }
}
