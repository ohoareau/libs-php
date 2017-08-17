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

use Itq\Common\Service\BusinessRuleService;
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

        $this->mock('tenantService')->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));
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
        $this->mock('tenantService')->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));
        $context = (object) ['counter' => 0, 'value' => 0];

        $brX001 = function () use ($context) {
            $context->counter++;
            $context->value += 1;
        };
        $brX002 = function () use ($context) {
            $context->counter++;
            $context->value /= 2;
        };

        $this->s()->register('X001', 'my first business rule', $brX001, ['model' => 'myModel', 'operation' => 'create']);
        $this->s()->register('X002', 'my second business rule', $brX002, ['model' => 'myModel', 'operation' => 'create']);

        $this->s()->executeBusinessRulesForModelOperation('myModel', 'create', (object) []);

        $this->assertEquals(2, $context->counter);
        $this->assertEquals(0.5, $context->value);
    }
    /**
     * @group unit
     */
    public function testExecuteModelOperationBusinessRulesExecuteAllBusinessRulesForTheSpecifiedTenantInRegisteredOrder()
    {
        $this->mock('tenantService')->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));
        $context = (object) ['counter' => 0, 'value' => 0];

        $brX001 = function () use ($context) {
            $context->counter++;
            $context->value += 1;
        };
        $brX002 = function () use ($context) {
            $context->counter++;
            $context->value /= 2;
        };

        $this->s()->register('X001', 'my first business rule', $brX001, ['model' => 'myModel', 'operation' => 'create', 'tenant' => ['testtenant' => false]]);
        $this->s()->register('X002', 'my second business rule', $brX002, ['model' => 'myModel', 'operation' => 'create']);

        $this->s()->executeBusinessRulesForModelOperation('myModel', 'create', (object) []);

        $this->assertEquals(1, $context->counter);
        $this->assertEquals(0, $context->value);
    }
}
