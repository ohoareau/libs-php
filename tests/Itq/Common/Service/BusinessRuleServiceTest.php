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

use Exception;
use RuntimeException;
use Itq\Common\ValidationContext;
use Itq\Common\Service\BusinessRuleService;
use Itq\Common\Exception as CommonException;
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
     * @param string          $method
     * @param array|Exception $expected
     * @param string          $modelName
     * @param string          $operation
     * @param array           $businessRules
     * @param object          $ctx
     * @param null|object     $doc
     * @param array           $options
     *
     * @group unit
     *
     * @dataProvider getExecuteData
     */
    public function testExecute($method, $expected, $modelName, $operation, $businessRules, $ctx, $doc = null, $options = [])
    {
        $ctx->value = null;
        $ctx->count = 0;

        if ($expected instanceof Exception) {
            $this->expectExceptionThrown($expected);
        }
        $this->mockedTenantService()->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));

        foreach ($businessRules as $id => $businessRule) {
            $this->s()->register($id, $businessRule[0], $businessRule[1], $businessRule[2]);
        }

        switch ($method) {
            case 'multipleWithContext':
                $this->s()->executeBusinessRulesForModelOperationWithExecutionContext(
                    new ValidationContext($this->mockedErrorManager()),
                    $modelName,
                    $operation,
                    $doc ?: (object) [],
                    $options
                );
                break;
            case 'BusinessRulesForModelOperation':
                $this->s()->executeBusinessRulesForModelOperation($modelName,$operation,$doc ?: (object) [],$options );
                break;
            case 'ModelOperation':
                $this->s()->executeModelOperation($modelName,$operation,$doc ?: (object) [], $options);
                break;
            case 'ModelOperation':
                $this->s()->executeModelOperation($modelName,$operation,$doc ?: (object) [], $options);
                break;
            case 'byId':
                $this->s()->executeBusinessRuleById('X01');
                break;
            default:
                throw new RuntimeException(sprintf("Unknown method '%s' for execute test", $method), 412);
        }

        foreach ($expected as $k => $v) {
            $this->assertEquals($v, $ctx->$k);
        }
    }
    /**
     * @return array
     */
    public function getExecuteData()
    {
        $ctx   = (object) [];

        $br = function () use ($ctx) {
            $ctx->count++;
            $ctx->value += 10;
        };

        $brException = function () use ($ctx) {
            throw new RuntimeException('There was an unexpected exception !', 502);
        };
        $brBRException = function () use ($ctx) {
            throw new CommonException\BusinessRuleException('There was an unexpected exception !', 502);
        };

        return [
            '0 - no business rules triggered' => [
                'multipleWithContext', ['value' => null, 'count' => 0], 'myModel', 'create', [], $ctx,
            ],
            '1 - one business rule triggered for exactly this model and operation' => [
                'multipleWithContext', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '2 - one business rule triggered for exactly this model but wildcard operation' => [
                'multipleWithContext', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => '*']]], $ctx,
            ],
            '3 - one business rule triggered for wildcard model but wildcard operation' => [
                'multipleWithContext', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => '*', 'operation' => '*']]], $ctx,
            ],
            '4 - one business rule triggered for wildcard model but this operation' => [
                'multipleWithContext', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => '*', 'operation' => 'create']]], $ctx,
            ],
            '5 - multiple business rules triggered' => [
                'multipleWithContext', ['value' => 20, 'count' => 2], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create']], 'X02' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '6 - multiple business rule executed and throw exception' => [
                'multipleWithContext', new RuntimeException('There was an unexpected exception !', 502), 'myModel', 'create', ['X01' => ['desc', $brException, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '7 - multiple business rule executed and throw BusinessRuleExeption' => [
                'multipleWithContext', new RuntimeException('There was an unexpected exception !', 502), 'myModel', 'create', ['X01' => ['desc', $brBRException, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '8 - no business rules registered for this specified id' => [
                'byId', new RuntimeException("Unknown business rule 'X01'", 404), null, null, [], $ctx,
            ],
            '9 - specified business rule executed' => [
                'byId', ['value' => 10, 'count' => 1], null, null, ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '10 - specified business rule executed and throw exception' => [
                'byId', new RuntimeException('There was an unexpected exception !', 502), null, null, ['X01' => ['desc', $brException, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '11 - specified business rule executed and throw BusinessRuleExeption' => [
                'byId', new RuntimeException('There was an unexpected exception !', 502), null, null, ['X01' => ['desc', $brBRException, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '12 - multiple business rule  for triggered model' => [
                'BusinessRulesForModelOperation', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create']]], $ctx,
            ],
            '13 - multiple business rules for triggered model with tenant' => [
                'BusinessRulesForModelOperation', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => 'create',  'tenant' => ['testtenant' => true]]]], $ctx,
            ],
            '14 - multiple business rules for triggered model but wildcard operation' => [
                'BusinessRulesForModelOperation', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => '*']]], $ctx,
            ],
            '15 - multiple business rules triggered for wildcard model and wildcard operation' => [
                'BusinessRulesForModelOperation', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => '*', 'operation' => '*']]], $ctx,
            ],
            '16 - multiple business rules triggered for wildcard model but this operation' => [
                'BusinessRulesForModelOperation', ['value' => 10, 'count' => 1], 'myModel', 'create', ['X01' => ['desc', $br, ['model' => '*', 'operation' => 'create']]], $ctx,
            ],
            '17 - multiple business rules Exception' => [
                'BusinessRulesForModelOperation', new RuntimeException('There was an unexpected exception !', 502), 'myModel', 'create', ['X01' => ['desc', $brException, ['model' => '*', 'operation' => 'create']]], $ctx,
            ],
            '18 - multiple business rules BusinessRuleExeption' => [
                'BusinessRulesForModelOperation', new RuntimeException('There was an unexpected exception !', 502), 'myModel', 'create', ['X01' => ['desc', $brBRException, ['model' => '*', 'operation' => 'create']]], $ctx,
            ],
            '19 - one triggered model and triggered operation' => [
                'ModelOperation', ['value' => 10, 'count' => 1], 'myModel', 'delete', ['X01' => ['desc', $br, ['model' => 'myModel', 'operation' => 'delete']]], $ctx,
            ],

        ];
    }

    /**
     * @group unit
     */
    public function testRegister()
    {
        $this->mockedTenantService()->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));
        $context = (object) ['counter' => 0, 'value' => 0];

        $brX001 = function () use ($context) {
            $context->counter++;
            $context->value += 2;
        };
        $this->expectExceptionThrown(new RuntimeException("Registered business rule must be a callable for 'X001'", 500));
        $this->s()->register('X001', 'my uncallable rule', [],  ['model' => 'myModel', 'operation' => 'create']);

        $this->s()->register('X001', 'my first rule', $brX001,  ['model' => 'myModel', 'operation' => 'create']);
        $this->expectExceptionThrown(new RuntimeException("A business rule with id 'X001' has already been registered (duplicated)", 500));
        $this->s()->register('X001', 'my first rule', $brX001,  ['model' => 'myModel', 'operation' => 'create']);

        $this->s()->register('X002', 'my second rule', $brX001,  ['model' => null, 'operation' => 'create']);
        $this->expectExceptionThrown(new RuntimeException("Unsupported business rule type for id 'X001'", 500));

  }
    /**
     * @group unit
     */
    public function testGetModelBusinessRules()
    {
        $this->mockedTenantService()->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));
        $context = (object) ['counter' => 0, 'value' => 0];

        $brX001 = function () use ($context) {
            $context->counter++;
            $context->value += 2;
        };

        $this->s()->register('X001', 'my first business rule', $brX001,  ['model' => 'myModel', 'operation' => 'create', 'tenant' => ['testtenant' => false]]);
        $this->s()->register('X002', 'my second business rule', $brX001, ['model' => 'myModel', 'operation' => 'delete']);
        $this->s()->register('X003', 'my third business rule', $brX001, ['model' => 'myModel2', 'operation' => 'delete']);

        $bs = $this->s()->getModelBusinessRules('myModel');
        $this->assertCount(1, $bs['delete']);
        $this->assertCount(1, $bs['create']);
    }
    /**
     * @group unit
     */
    public function testGetFlattenBusinessRuleDefinitions()
    {
        $this->assertCount(0, $this->s()->getFlattenBusinessRuleDefinitions());

        $this->mockedTenantService()->expects($this->any())->method('getCurrent')->will($this->returnValue('testtenant'));
        $context = (object) ['counter' => 0, 'value' => 0];

        $brX001 = function () use ($context) {
            $context->counter++;
            $context->value += 2;
        };

        $this->s()->register('X001', 'my first business rule', $brX001,  ['model' => 'myModel', 'operation' => 'create', 'tenant' => ['testtenant' => true ]]);
        $this->s()->register('X002', 'my second business rule', $brX001,  ['model' => 'myModel', 'operation' => 'create', 'tenant' => ['testtenant' => false ]]);
        $this->s()->register('X003', 'my third business rule', $brX001, ['model' => 'myModel', 'operation' => 'create']);

        $this->assertCount(3, $this->s()->getFlattenBusinessRuleDefinitions());
    }

}
