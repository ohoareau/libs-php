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

use Itq\Common\Service\VolatileSubDocumentService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;
use Tests\Itq\Common\Service\Stub\VolatileModel;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/volatile-sub-document
 */
class VolatileSubDocumentServiceTest extends AbstractServiceTestCase
{
    /**
     * @return VolatileSubDocumentService
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
     * @group document
     */
    public function testGetTypes()
    {
        $this->s()->setTypes(['a', 'b']);
        $this->assertEquals(['a', 'b'], $this->s()->getTypes());
    }
    /**
     * @group unit
     * @group document
     */
    public function testFullType()
    {
        $this->s()->setTypes(['a', 'b']);
        $this->assertEquals('a.b', $this->s()->getFullType());

        $this->s()->setTypes(['a', 'b']);
        $this->assertEquals('a b', $this->s()->getFullType(' '));
    }
    /**
     * @group unit
     */
    public function testSaveCreateBulk()
    {
        $bulk = [['id_1' => 'obj1', ], ];

        $m = $this->accessible($this->s(), 'saveCreateBulk');
        $this->assertEquals($bulk, $m->invoke($this->s(), 'parent_id', $bulk));
    }
    /**
     * @group unit
     */
    public function testCreate()
    {
        $this->s()->setTypes(['a', 'b']);
        $data = [
            'data1' => 1,
            'data2' => 2,
        ];

        $this->mockedReturn(
            $this->mockedFormService(),
            'validate',
            function (...$args) {
                return $this->toObject(VolatileModel::class, $args[2]);
            }
        );
        $this->mockedReturn($this->mockedModelService(), 'refresh', 0);
        $this->mockedReturn(
            $this->mockedModelService(),
            'convertObjectToArray',
            function (...$args) {
                return [$args[1], (array) $args[1], ];
            }
        );
        $this->mockedReturn($this->mockedBusinessRuleService(), 'executeBusinessRulesForModelOperation', 2);
        $this->mockedReturn($this->mockedModelService(), 'clean', 0);
        $this->mockedEventDispatcher()->expects($this->once())->method('dispatch')->with('a.b.created');

        $returned = $this->s()->create('p_id', $data);
        $this->assertEquals($data['data1'], $returned->data1);
        $this->assertEquals($data['data2'], $returned->data2);
    }
    /**
     * @group unit
     */
    public function testCreateBulk()
    {
        $this->s()->setTypes(['a', 'b']);
        $data = [
            ['data1' => 1, 'data2' => 2],
            ['data1' => 3, 'data2' => 4],

        ];

        $this->mockedReturn(
            $this->mockedFormService(),
            'validate',
            function (...$args) {
                return $this->toObject(VolatileModel::class, $args[2]);
            }
        );
        $this->mockedReturn($this->mockedModelService(), 'refresh', 0);
        $this->mockedReturn(
            $this->mockedModelService(),
            'convertObjectToArray',
            function (...$args) {
                return [$args[1], (array) $args[1], ];
            }
        );
        $this->mockedReturn($this->mockedBusinessRuleService(), 'executeBusinessRulesForModelOperation', 2);
        $this->mockedReturn($this->mockedModelService(), 'clean', 0);
        $this->mockedEventDispatcher()->expects($this->exactly(2))->method('dispatch')->with('a.b.created');

        $returned = $this->s()->createBulk('p_id', $data);

        $this->assertCount(2, $returned);
        $first  = array_shift($returned);
        $second = array_shift($returned);

        $this->assertObjectHasAttribute('id', $first);
        $this->assertObjectHasAttribute('id', $second);
        $this->assertEquals($data[0]['data1'], $first->data1);
        $this->assertEquals($data[0]['data2'], $first->data2);
        $this->assertEquals($data[1]['data1'], $second->data1);
        $this->assertEquals($data[1]['data2'], $second->data2);
    }
}
