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
        $this->mockMethodOnce('getArrayParameter', ['typeNames'], $params);

        $this->assertEquals($params, $this->invokeProtected('listCallablesByType', 'typeName'));
    }
    /**
     * @group unit
     */
    public function testCheckCallableWithNoCallableThrowRuntimeException()
    {
        $this->expectExceptionThrown(new \RuntimeException('runtimeException', 412));

        $this->mockMethodOnce(
            'createUnexpectedException',
            'Not a valid callable',
            new \RuntimeException('runtimeException', 412)
        );

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
        $this->mockMethodOnce(
            'setArrayParameterKey',
            ['type1s', 'callable', ['type' => 'callable', 'callable' => $callable, 'options' => []]]
        );

        $this->invokeProtected('registerCallableByType', 'type1', 'callable', $callable);
    }
    /**
     * @group unit
     */
    public function testRegisterCallableSetByTypeWithMissingSubItemsNameThrowRuntimeException()
    {
        $subItems = [['name' => 'name1', 'some datas'], ['missing name']];

        $this->expectExceptionThrown(new \RuntimeException('runtimeException', 412));

        $this->mockMethodOnce(
            'createRequiredException',
            ['Missing name for %s #%d in set \'%s\'', 'typeMissingName', 1, 'testName'],
            new \RuntimeException('runtimeException', 412)
        );

        $this->invokeProtected('registerCallableSetByType', 'typeMissingName', 'testName', $subItems);
    }
    /**
     * @group unit
     */
    public function testRegisterCallableSetByType()
    {
        $subItems = [['name' => 'name1', 'some datas 1'], ['name' => 'name2', 'some datas 2']];
        $this->mockMethodOnce(
            'setArrayParameterKey',
            ['typeNames', 'testName', ['type' => 'set', 'subItems' => $subItems, 'options' => []]]
        );

        $this->invokeProtected('registerCallableSetByType', 'typeName', 'testName', $subItems);
    }
    /**
     * @group unit
     */
    public function testGetCallableByType()
    {
        $this->mockMethodOnce('getArrayParameterKey', ['typeNames', 'testName'], ['some parameters']);

        $this->assertEquals(['some parameters'], $this->invokeProtected('getCallableByType', 'typeName', 'testName'));
    }
    /**
     * @group unit
     */
    public function testHasCallableByType()
    {
        $this->mockMethodOnce('hasArrayParameterKey', ['typeNames', 'testName'], true);

        $this->assertTrue($this->invokeProtected('hasCallableByType', 'typeName', 'testName'));
    }
    /**
     * @group unit
     */
    public function testFindCallablesByType()
    {
        $this->mockMethodOnce('getArrayParameter', 'typeNames', ['some parameters']);

        $this->assertEquals(['some parameters'], $this->invokeProtected('findCallablesByType', 'typeName'));
    }
    /**
     * @group unit
     */
    public function testExecuteCallableByTypeWithMissingCallableTypeThrowRuntimeException()
    {
        $this->expectExceptionThrown(new \RuntimeException('runtimeException', 412));

        $this->mockMethodOnce('getArrayParameterKey', ['typeNames', 'testName'], ['type' => 'unknownType']);
        $this->mockMethodOnce(
            'createUnexpectedException',
            ["Unsupported callable type '%s'", 'unknownType'],
            new \RuntimeException('runtimeException', 412)
        );

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

        $this->mockMethodOnce('getArrayParameterKey', ['typeNames', 'testName'], $params);

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

        $this->mockMethodAt(0, 'getArrayParameterKey', ['typeNames', 'testName'], $params);
        $this->mockMethodAt(1, 'getArrayParameterKey', ['typeNames', 'nameSubItem'], $paramSubItem);

        $this->assertEquals($this->t(), $this->invokeProtected('executeCallableByType', 'typeName', 'testName'));
    }
    /**
     * @group unit
     */
    public function testExecuteCallableListByTypeWithMissingCallableNameThrowRuntimeException()
    {

        $this->expectExceptionThrown(new \RuntimeException('runtimeException', 412));

        $this->mockMethodOnce(
            'createRequiredException',
            ['Missing %s name (step #%d)', 'typeMissingName', 0],
            new \RuntimeException('runtimeException', 412)
        );

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
            $this->mockMethodAt(0, 'getArrayParameterKey', ['typeNames', 'callable1'], $params[0]);
        }

        if (2 === count($callables)) {
            $this->mockMethodAt(1, 'getArrayParameterKey', ['typeNames', 'callable2'], $params[1]);
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
