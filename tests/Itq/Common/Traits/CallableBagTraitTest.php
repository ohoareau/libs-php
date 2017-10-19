<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Traits;

use Itq\Common\Traits\CallableBagTrait;
use Itq\Common\Tests\Traits\Base\AbstractTraitTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group traits
 * @group traits/callable-bag
 */
class CallableBagTraitTest extends AbstractTraitTestCase
{
    /**
     * @return CallableBagTrait|\PHPUnit_Framework_MockObject_MockObject
     */
    public function t()
    {
        return parent::t();
    }
    /**
     * @group unit
     */
    public function testListCallablesByType()
    {
        $params = ['some parameters'];
        $this->t()->expects($this->once())
            ->method('getArrayParameter')
            ->with('typeNames')
            ->will($this->returnValue($params));

        $this->assertEquals($params, $this->invokeProtected('listCallablesByType', 'typeName'));
    }
    /**
     * @group unit
     */
    public function testCheckCallableWithNoCallableThrowRuntimeException()
    {
        $this->expectExceptionThrown(new \RuntimeException('runtimeException', 412));

        $this->t()->expects($this->once())
            ->method('createUnexpectedException')
            ->with('Not a valid callable')
            ->will($this->returnValue(new \RuntimeException('runtimeException', 412)));

        $this->invokeProtected('checkCallable', 'not a callable');
    }
    /**
     * @group unit
     */
    public function testRegisterCallableByType()
    {
        $callable = function () {
            return 1;
        };

        $this->t()->expects($this->once())
            ->method('setArrayParameterKey')
            ->with('type1s', 'callable', ['type' => 'callable', 'callable' => $callable, 'options' => []]);

        $this->invokeProtected('registerCallableByType', 'type1', 'callable', $callable);
    }
    /**
     * @group unit
     */
    public function testRegisterCallableSetByTypeWithMissingSubItemsNameThrowRuntimeException()
    {
        $subItems = [['name' => 'name1', 'some datas'], ['missing name']];

        $this->expectExceptionThrown(new \RuntimeException('runtimeException', 412));

        $this->t()->expects($this->once())
            ->method('createRequiredException')
            ->with('Missing name for %s #%d in set \'%s\'', 'typeMissingName', 1, 'testName')
            ->will($this->returnValue(new \RuntimeException('runtimeException', 412)));

        $this->invokeProtected('registerCallableSetByType', 'typeMissingName', 'testName', $subItems);
    }
    /**
     * @group unit
     */
    public function testRegisterCallableSetByType()
    {
        $subItems = [['name' => 'name1', 'some datas 1'], ['name' => 'name2', 'some datas 2']];
        $this->t()->expects($this->once())
            ->method('setArrayParameterKey')
            ->with('typeNames', 'testName', ['type' => 'set', 'subItems' => $subItems, 'options' => []]);

        $this->invokeProtected('registerCallableSetByType', 'typeName', 'testName', $subItems);
    }
    /**
     * @group unit
     */
    public function testGetCallableByType()
    {
        $this->t()->expects($this->once())
            ->method('getArrayParameterKey')
            ->with('typeNames', 'testName')
            ->will($this->returnValue(['some parameters']));

        $this->assertEquals(['some parameters'], $this->invokeProtected('getCallableByType', 'typeName', 'testName'));
    }
    /**
     * @group unit
     */
    public function testHasCallableByType()
    {
        $this->t()->expects($this->once())
            ->method('hasArrayParameterKey')
            ->with('typeNames', 'testName')
            ->will($this->returnValue(true));

        $this->assertTrue($this->invokeProtected('hasCallableByType', 'typeName', 'testName'));
    }
    /**
     * @group unit
     */
    public function testFindCallablesByType()
    {
        $this->t()->expects($this->once())
            ->method('getArrayParameter')
            ->with('typeNames')
            ->will($this->returnValue(['some parameters']));

        $this->assertEquals(['some parameters'], $this->invokeProtected('findCallablesByType', 'typeName'));
    }
    /**
     * @group unit
     */
    public function testExecuteCallableByTypeWithMissingCallableTypeThrowRuntimeException()
    {
        $this->expectExceptionThrown(new \RuntimeException('runtimeException', 412));

        $this->t()->expects($this->once())
            ->method('getArrayParameterKey')
            ->with('typeNames', 'testName')
            ->will($this->returnValue(['type' => 'unknownType']));

        $this->t()->expects($this->once())
            ->method('createUnexpectedException')
            ->with("Unsupported callable type '%s'", 'unknownType')
            ->will($this->returnValue(new \RuntimeException('runtimeException', 412)));
        $this->invokeProtected('executeCallableByType', 'typeName', 'testName');
    }
    /**
     * @group unit
     */
    public function testExecuteCallableByTypeWithCallableType()
    {
        $params = [
            'type'     => 'callable',
            'callable' => function () {
                return "I've been called";
            },
            'options'  => [],
        ];

        $this->t()->expects($this->at(0))
            ->method('getArrayParameterKey')
            ->with('typeNames', 'testName')
            ->will($this->returnValue($params));

        $this->assertEquals("I've been called", $this->invokeProtected('executeCallableByType', 'typeName', 'testName'));
    }
    /**
     * @group unit
     */
    public function testExecuteCallableByTypeWithSetType()
    {
        $params = ['type' => 'set', 'subItems' => [['name' => 'nameSubItem']], 'options' => []];
        $paramSubItem = [
            'type'     => 'callable',
            'callable' => function () {
                return "i've been called as subItem";
            },
            'options'  => [],
        ];

        $this->t()->expects($this->at(0))
            ->method('getArrayParameterKey')
            ->with('typeNames', 'testName')
            ->will($this->returnValue($params));

        $this->t()->expects($this->at(0))
            ->method('getArrayParameterKey')
            ->with('typeNames', 'testName')
            ->will($this->returnValue($params));

        $this->t()->expects($this->at(1))
            ->method('getArrayParameterKey')
            ->with('typeNames', 'nameSubItem')
            ->will($this->returnValue($paramSubItem));

        $this->assertEquals($this->t(), $this->invokeProtected('executeCallableByType', 'typeName', 'testName'));
    }
    /**
     * @group unit
     */
    public function testExecuteCallableListByTypeWithMissingCallableNameThrowRuntimeException()
    {

        $this->expectExceptionThrown(new \RuntimeException('runtimeException', 412));

        $this->t()->expects($this->once())
            ->method('createRequiredException')
            ->with('Missing %s name (step #%d)', 'typeMissingName', 0)
            ->will($this->returnValue(new \RuntimeException('runtimeException', 412)));

        $this->invokeProtected('executeCallableListByType', 'typeMissingName', ['missing name']);
    }
    /**
     * @param array    $callables
     * @param array    $params
     * @param \Closure $conditionCallable
     *
     * @group unit
     *
     * @dataProvider getExecuteCallableListByType
     */
    public function testExecuteCallableListByType($callables, $params, $conditionCallable)
    {
        if ('callableNotBeenCalled' !== $callables[0]['name']) {
            $this->t()->expects($this->at(0))
                ->method('getArrayParameterKey')
                ->with('typeNames', 'callable1')
                ->will($this->returnValue($params[0]));
        }

        if (2 === count($callables)) {
            $this->t()->expects($this->at(1))
                ->method('getArrayParameterKey')
                ->with('typeNames', 'callable2')
                ->will($this->returnValue($params[1]));
        }
        $this->invokeProtected('executeCallableListByType', 'typeName', $callables, [], $conditionCallable);
    }
    /**
     * @return array
     */
    public function getExecuteCallableListByType()
    {
        return [
            '0 - simple test with 2 callables'       => [
                [['name' => 'callable1'], ['name' => 'callable2']],
                [
                    [
                        'type'     => 'callable',
                        'callable' => function () {
                            return "i've been called";
                        },
                        'options'  => [],
                    ],
                    [
                        'type'     => 'callable',
                        'callable' => function () {
                            return "i've been called";
                        },
                        'options'  => [],
                    ],
                ],
                null,
            ],
            '1 - condition callable returning true'  => [
                [['name' => 'callable1']],
                [
                    [
                        'type'     => 'callable',
                        'callable' => function () {
                            return "i've been called";
                        },
                        'options'  => [],
                    ],
                ],
                function () {
                    return true;
                },
            ],
            '2 - condition callable returning false' => [
                [['name' => 'callableNotBeenCalled']],
                [
                    [
                        'type'     => 'callable',
                        'callable' => function () {
                            return "i've been called";
                        },
                        'options'  => [],
                    ],
                ],
                function () {
                    return false;
                },
            ],
        ];
    }
}
