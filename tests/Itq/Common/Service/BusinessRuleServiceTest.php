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

        $this->expectExceptionMessage('Registered business rule must be a callable for');
        $this->s()->register('X003', 'my fours existing business rule', $brX002, ['model' => 'myModel', 'operation' => 'create', 'tenant' => ['testtenant' => false]]);

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
     * @group unit
     */
    public function testExecuteBusinessRulesForModelOperationWithExecutionContext()
    {
        $context = $this->getRegisteredService();

        $validationContext = new ValidationContext($this->mockedErrorManager());
        $this->s()->executeBusinessRulesForModelOperationWithExecutionContext($validationContext,'myModel', 'create',(object) [], array());
        $this->assertEquals(3, $context->counter);
        $this->assertEquals(4, $context->value);

        $this->s()->executeBusinessRulesForModelOperationWithExecutionContext($validationContext,'myModel', '*',(object) [], array());
        $this->assertEquals(3, $context->counter);
        $this->assertEquals(9, $context->value);


        $this->s()->executeBusinessRulesForModelOperationWithExecutionContext($validationContext,'myModel', 'update',(object) [], array());
        $this->assertEquals(8, $context->counter);
        $this->assertEquals(15, $context->value);
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
