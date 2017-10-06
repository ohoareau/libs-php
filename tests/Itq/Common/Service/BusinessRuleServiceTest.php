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

use Itq\Common\ValidationContext;
use Itq\Common\Service\BusinessRuleService;
use Itq\Common\Exception\BusinessRuleException;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/business-rule
 */
class BusinessRuleServiceTest extends AbstractServiceTestCase
{
    /**
     * @return BusinessRuleService
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
        return [
            $this->mockedTenantService(),
            $this->mockedContextService(),
        ];
    }
    /**
     * @group unit
     */
    public function testAddBusinessRuleForUnknownTypeThrowException()
    {
        $brX001 = function () {
        };

        $this->mockedTenantService()->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Unsupported business rule type for id 'X001'");
        $this->expectExceptionCode(500);
        $this->s()->register('X001', 'my business rule', $brX001);

        $this->assertEquals(['callback' => $brX001, 'code' => 'X001', 'params' => []], $this->s()->getBusinessRuleById('X001'));
    }
    /**
     * @group unit
     */
    public function testExecuteModelOperationBusinessRulesExecuteAllBusinessRulesInRegisteredOrder()
    {
        $context = $this->getRegisteredService();
        $this->s()->executeBusinessRulesForModelOperation('myModel', 'create', (object) []);

        $this->assertEquals(2, $context->counter);
        $this->assertEquals(3, $context->value);

    }
    /**
     * @group unit
     */
    public function testExecuteModelOperationBusinessRulesExecuteAllBusinessRulesForTheSpecifiedTenantInRegisteredOrder()
    {
        $context = $this->getRegisteredService();
        $this->s()->executeBusinessRulesForModelOperation('myModel', 'create', (object) []);

        $this->assertEquals(2, $context->counter);
        $this->assertEquals(3, $context->value);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Registered business rule must be a callable for');
        $this->s()->register('X003', 'my third not callable business rule', 'uncallable', ['model' => 'myModel', 'operation' => 'create', 'tenant' => ['testtenant' => false]]);

//        $this->expectExceptionMessage('Registered business rule must be a callable for');
//        $this->s()->register('X003', 'my fours existing business rule', $brX002, ['model' => 'myModel', 'operation' => 'create', 'tenant' => ['testtenant' => false]]);

    }
    /**
     * @group unit
     */
    public function testGetFlattenBusinessRuleDefinitions()
    {
        $this->getRegisteredService();
        $this->assertCount(5, $this->s()->getFlattenBusinessRuleDefinitions());
    }
    /**
     * @param mixed       $expectedValue
     * @param int         $expectedCount
     * @param string      $modelName
     * @param string      $operation
     * @param array       $businessRules
     * @param object      $ctx
     * @param null|object $doc
     * @param array       $options
     *
     * @group unit
     *
     * @dataProvider getExecuteBusinessRulesForModelOperationWithExecutionContextData
     */
    public function testExecuteBusinessRulesForModelOperationWithExecutionContext($expectedValue, $expectedCount, $modelName, $operation, $businessRules, $ctx, $doc = null, $options = [])
    {
        $ctx->value = null;
        $ctx->count = 0;

        foreach ($businessRules as $id => $businessRule) {
            $this->s()->register($id, $businessRule[0], $businessRule[1], $businessRule[2]);
        }

        $this->s()->executeBusinessRulesForModelOperationWithExecutionContext(
            new ValidationContext($this->mockedErrorManager()),
            $modelName,
            $operation,
            $doc ?: (object) [],
            $options
        );

        $this->assertEquals($expectedCount, $ctx->count);
        $this->assertEquals($expectedValue, $ctx->value);
    }
    /**
     * @return array
     */
    public function getExecuteBusinessRulesForModelOperationWithExecutionContextData()
    {
        $ctx   = (object) [];

        $br = function () use ($ctx) {
            $ctx->count++;
            $ctx->value += 10;
        };

        return [
            '0 - no business rules triggered' => [
                null, 0, 'myModel', 'create', [], $ctx,
            ],
            '1 - one business rule triggered for exactly this model and operation' => [
                10, 1, 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '2 - one business rule triggered for exactly this model but wildcard operation' => [
                10, 1, 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => '*']]], $ctx,
            ],
            '3 - one business rule triggered for wildcard model but wildcard operation' => [
                10, 1, 'myModel', 'create', ['X01' => ['desc', $br, ['model' => '*', 'operation' => '*']]], $ctx,
            ],
            '4 - one business rule triggered for wildcard model but this operation' => [
                10, 1, 'myModel', 'create', ['X01' => ['desc', $br, ['model' => '*', 'operation' => 'create']]], $ctx,
            ],
            '5 - multiple business rules triggered' => [
                20, 2, 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create']], 'X02' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
        ];
    }
    /**
     * @group unit
     */
    public function testExecuteBusinessRuleById()
    {
        $context = $this->getRegisteredService();

        $this->s()->executeBusinessRuleById('X001');
        $this->assertEquals(1, $context->counter);
        $this->s()->executeBusinessRuleById('X002');
        $this->assertEquals(2, $context->counter);
        $this->expectExceptionMessage('Unknown business rule \'X003\'');
        $this->s()->executeBusinessRuleById('X003');

    }
    /**
     * @group unit
     */
    public function testExecuteModelOperation()
    {
        $context = $this->getRegisteredService();
        $this->s()->executeModelOperation('myModel','delete',(object) []);
        $this->assertEquals(2, $context->counter);
        $this->assertEquals(45, $context->value);
    }
    /**
     * @group unit
     */
    public function testGetModelBusinessRules()
    {
        $this->getRegisteredService();
        $bs = $this->s()->getModelBusinessRules('myModel');
        $this->assertCount(1, $bs['delete']);
        $this->assertCount(2, $bs['create']);
    }
    /**
     *
     */
    private function getRegisteredService()
    {
        $this->mockedTenantService()->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));
        $context = (object) ['counter' => 0, 'value' => 0];

        $brX001 = function () use ($context) {
            $context->counter++;
            $context->value += 2;
        };
        $brX002 = function () use ($context) {
            $context->counter++;
            $context->value /= 2;
        };
        $brX004 = function () use ($context) {
            $context->counter++;
            $context->value += 3;
        };
        $brX005 = function () use ($context) {
            $context->counter++;
            $context->value += 42;
        };
        $brX006 = function () use ($context) {
            $context->counter++;
            $context->value = 100;
        };

        $this->s()->register('X001', 'my first business rule', $brX001,  ['model' => 'myModel', 'operation' => 'create', 'tenant' => ['testtenant' => false]]);
        $this->s()->register('X002', 'my second business rule', $brX002, ['model' => 'myModel', 'operation' => 'create']);
        $this->s()->register('X004', 'my fourth business rule', $brX004, ['model' => 'myModel', 'operation' => '*', 'tenant' => ['testtenant' => true]]);
        $this->s()->register('X005', 'my fifth business rule', $brX005,  ['model' => 'myModel', 'operation' => 'delete', 'tenant' => ['testtenant' => true]]);
        $this->s()->register('X006', 'my sixth business rule',  $brX006, ['model' => '*',       'operation' => 'update']);

        return $context;
    }


}
