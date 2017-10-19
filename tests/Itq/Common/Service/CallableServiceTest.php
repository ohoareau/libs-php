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

use Itq\Common\Service\CallableService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/callable
 */
class CallableServiceTest extends AbstractServiceTestCase
{
    /**
     * @return CallableService|PHPUnit_Framework_MockObject_MockObject
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testListByType()
    {
        $this->mockMethodOnce('listCallablesByType', 'nameType', ['some callable data']);
        $this->assertEquals(['some callable data'], $this->s()->listByType('nameType'));
    }
    /**
     * @group unit
     */
    public function testRegisterByType()
    {
        $callable = function () {
            return true;
        };
        $this->mockMethodOnce('registerCallableByType', ['typeName', 'nameTest', $callable, ['some options']]);
        $this->s()->registerByType('typeName', 'nameTest', $callable, ['some options']);
    }
    /**
     * @group unit
     */
    public function testRegisterSetByType()
    {
        $this->mockMethodOnce('registerCallableSetByType', ['typeName', 'nameTest', ['list subitems'], ['some options']]);
        $this->s()->registerSetByType('typeName', 'nameTest', ['list subitems'], ['some options']);
    }
    /**
     * @group unit
     */
    public function testGetByType()
    {
        $this->mockMethodOnce('getCallableByType', ['typeName', 'nameTest'], ['callable datas']);
        $this->assertEquals(['callable datas'], $this->s()->getByType('typeName', 'nameTest'));
    }
    /**
     * @group unit
     */
    public function testHasByType()
    {
        $this->mockMethodOnce('hasCallableByType', ['typeName', 'nameTest'], true);
        $this->assertTrue($this->s()->hasByType('typeName', 'nameTest'));
    }
    /**
     * @group unit
     */
    public function testFindByType()
    {
        $this->mockMethodOnce('findCallablesByType', ['typeName'], ['callable type datas']);
        $this->assertEquals(['callable type datas'], $this->s()->findByType('typeName'));
    }
    /**
     * @group unit
     */
    public function testExecuteByType()
    {
        $this->mockMethodOnce('executeCallableByType', ['typeName', 'nameTest', [], null], 'exec return');
        $this->assertEquals('exec return', $this->s()->executeByType('typeName', 'nameTest'));
    }
    /**
     * @group unit
     */
    public function testExecute()
    {
        $callable = function () {
            return true;
        };
        $this->mockMethodOnce('executeCallable', [$callable, [], []], 'exec return');
        $this->assertEquals('exec return', $this->s()->execute($callable));
    }
    /**
     * @group unit
     */
    public function testExecuteListByType()
    {
        $this->mockMethodOnce('executeCallableListByType', ['typeName', ['callables list'], [], null], 'execs return');
        $this->assertEquals('execs return', $this->s()->executeListByType('typeName', ['callables list']));
    }
    /**
     * @return array
     */
    protected function getMockedMethod()
    {
        return [
            'listCallablesByType', 'registerCallableByType', 'registerCallableSetByType', 'getCallableByType',
            'hasCallableByType', 'findCallablesByType', 'executeCallableByType', 'executeCallable',
            'executeCallableListByType',
        ];
    }
}
