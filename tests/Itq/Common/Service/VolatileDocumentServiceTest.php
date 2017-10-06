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

use Itq\Common\Service;
use Tests\Itq\Common\Service\Stub\VolatileModel;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/volatile-document
 */
class VolatileDocumentServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\VolatileDocumentService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @return array
     */
    public function initializer()
    {
        $this->s()->setFormService($this->mockedFormService());
        $this->s()->setModelService($this->mockedModelService());
        $this->s()->setBusinessRuleService($this->mockedBusinessRuleService());
        $this->s()->setEventDispatcher($this->mockedEventDispatcher());
    }
    /**
     * @group unit
     */
    public function testGetTypes()
    {
        $this->s()->setTypes(['a']);
        $this->assertEquals(['a'], $this->s()->getTypes());
    }
    /**
     * @group unit
     */
    public function testFullType()
    {
        $this->s()->setTypes(['a']);
        $this->assertEquals('a', $this->s()->getFullType());
    }
    /**
     * @group unit
     */
    public function testCreate()
    {
        $this->s()->setTypes(['volatilemodel']);
        $data = [
            'data1' => 1,
            'data2' => 2,
        ];

        $this->mockedReturn($this->mockedFormService(), 'validate', function(...$args){return $this->toObject( VolatileModel::class , $args[2]);});
        $this->mockedReturn($this->mockedModelService(), 'refresh', 0);
        $this->mockedReturn($this->mockedModelService(), 'convertObjectToArray', function(...$args){ return (array) $args[0]; });
        $this->mockedReturn($this->mockedBusinessRuleService(), 'executeBusinessRulesForModelOperation', 2);
        $this->mockedReturn($this->mockedModelService(), 'clean', 0);
        $this->mockedEventDispatcher()->expects($this->once())->method('dispatch')->with('volatilemodel.created');

        $this->assertEquals($this->toObject(VolatileModel::class, $data), $this->s()->create($data));
    }
    /**
     * @group unit
     */
    public function testCreateBulk()
    {
        $this->markTestSkipped('@todo : fix mongo insert $data');
        $this->s()->setTypes(['volatilemodel']);
        $data = [
            ['data1' => 1, 'data2' => 2],
            ['data1' => 3, 'data2' => 4],
        ];

        $this->mockedReturn($this->mockedFormService(), 'validate', function(...$args){ return $this->toObject(VolatileModel::class , $args[2]); });
        $this->mockedReturn($this->mockedModelService(), 'refresh', 0);
        $this->mockedReturn($this->mockedModelService(), 'convertObjectToArray', function(...$args){ return (array) $args[0]; });
        $this->mockedReturn($this->mockedBusinessRuleService(), 'executeBusinessRulesForModelOperation', 2);
        $this->mockedReturn($this->mockedModelService(), 'clean', 0);
        $this->mockedEventDispatcher()->expects($this->exactly(2))->method('dispatch')->with('volatilemodel.created');

        $returned = $this->s()->createBulk($data);

        $this->assertCount(2, $returned);
        $this->assertEquals($this->toObject(a::class, $data[0]), array_shift($returned));
        $this->assertEquals($this->toObject(a::class, $data[1]), array_shift($returned));
    }
}