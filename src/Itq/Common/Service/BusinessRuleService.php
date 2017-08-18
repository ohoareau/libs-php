<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source id.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\Exception;
use Itq\Common\WorkflowExecutorInterface;
use Itq\Common\ValidationContextInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BusinessRuleService implements WorkflowExecutorInterface
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\TenantServiceAwareTrait;
    use Traits\ServiceAware\ContextServiceAwareTrait;
    /**
     * @var array
     */
    protected $businessRules = ['models' => [], 'ids'];
    /**
     * @param Service\TenantService  $tenantService
     * @param Service\ContextService $contextService
     */
    public function __construct(Service\TenantService $tenantService, Service\ContextService $contextService)
    {
        $this->setTenantService($tenantService);
        $this->setContextService($contextService);
    }
    /**
     * Return the list of registered business rules.
     *
     * @return array[]
     */
    public function getBusinessRules()
    {
        return $this->businessRules;
    }
    /**
     * @param string $modelName
     *
     * @return array
     */
    public function getModelBusinessRules($modelName)
    {
        return isset($this->businessRules['models'][$modelName]) ? $this->businessRules['models'][$modelName] : [];
    }
    /**
     * @param string $modelName
     * @param string $operation
     *
     * @return array
     */
    public function getModelOperationBusinessRules($modelName, $operation)
    {
        $businessRules = isset($this->businessRules['models'][$modelName][$operation]) ? $this->businessRules['models'][$modelName][$operation] : [];

        foreach ($businessRules as $k => $b) {
            if (!isset($b['params']['tenant'])) {
                continue;
            }
            if (!isset($b['params']['tenant'][$this->getTenantService()->getCurrent()]) || true !== $b['params']['tenant'][$this->getTenantService()->getCurrent()]) {
                unset($businessRules[$k]);
            }
        }

        return $businessRules;
    }
    /**
     * @param string $id
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getBusinessRuleById($id)
    {
        if (!isset($this->businessRules['ids'][$id])) {
            throw $this->createNotFoundException("Unknown business rule '%s'", $id);
        }

        return $this->businessRules['ids'][$id];
    }
    /**
     * Register an event action for the specified name (replace if exist).
     *
     * @param string   $id
     * @param string   $name
     * @param callable $callable
     * @param array    $params
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($id, $name, $callable, array $params = [])
    {
        if (!is_callable($callable)) {
            throw $this->createUnexpectedException("Registered business rule must be a callable for '%s'", $id);
        }

        if (isset($params['model'])) {
            $model = $params['model'];
            unset($params['model']);
            $params   += ['operation' => '*'];
            $operation = $params['operation'];
            unset($params['operation']);
            if (!isset($this->businessRules['models'][$model])) {
                $this->businessRules['models'][$model] = [];
            }
            if (!isset($this->businessRules['models'][$model][$operation])) {
                $this->businessRules['models'][$model][$operation] = [];
            }
            if (isset($this->businessRules['ids'][$id])) {
                throw $this->createDuplicatedException("A business rule with id '%s' has already been registered (duplicated)", $id);
            }

            $this->businessRules['models'][$model][$operation][$id] = ['callable' => $callable, 'params' => $params, 'id' => $id, 'name' => $name];
            $this->businessRules['ids'][$id] = &$this->businessRules['models'][$model][$operation][$id];

            return $this;
        }

        throw $this->createUnexpectedException("Unsupported business rule type for id '%s'", $id);
    }
    /**
     * @param string $modelName
     * @param string $operation
     * @param mixed  $model
     * @param array  $options
     *
     * @return $this
     */
    public function executeBusinessRulesForModelOperation($modelName, $operation, $model, array $options = [])
    {
        foreach ($this->getModelOperationBusinessRules($modelName, $operation) as $businessRule) {
            $this->executeBusinessRuleForModelOperation($modelName, $operation, $businessRule, $model, $options);
        }
        foreach ($this->getModelOperationBusinessRules($modelName, '*') as $businessRule) {
            $this->executeBusinessRuleForModelOperation($modelName, $operation, $businessRule, $model, $options);
        }
        foreach ($this->getModelOperationBusinessRules('*', $operation) as $businessRule) {
            $this->executeBusinessRuleForModelOperation($modelName, $operation, $businessRule, $model, $options);
        }
        foreach ($this->getModelOperationBusinessRules('*', '*') as $businessRule) {
            $this->executeBusinessRuleForModelOperation($modelName, $operation, $businessRule, $model, $options);
        }

        return $this;
    }
    /**
     * @param string $modelName
     * @param string $operation
     * @param mixed  $model
     * @param array  $options
     *
     * @return $this
     */
    public function executeModelOperation($modelName, $operation, $model, array $options = [])
    {
        return $this->executeBusinessRulesForModelOperation($modelName, $operation, $model, $options);
    }
    /**
     * @param ValidationContextInterface $context
     * @param string                     $modelName
     * @param string                     $operation
     * @param mixed                      $model
     * @param array                      $options
     *
     * @return $this
     */
    public function executeBusinessRulesForModelOperationWithExecutionContext(ValidationContextInterface $context, $modelName, $operation, $model, array $options = [])
    {
        foreach ($this->getModelOperationBusinessRules($modelName, $operation) as $businessRule) {
            $this->executeBusinessRuleForModelOperationWithExecutionContext($context, $modelName, $operation, $businessRule, $model, $options);
        }
        foreach ($this->getModelOperationBusinessRules($modelName, '*') as $businessRule) {
            $this->executeBusinessRuleForModelOperationWithExecutionContext($context, $modelName, $operation, $businessRule, $model, $options);
        }
        foreach ($this->getModelOperationBusinessRules('*', $operation) as $businessRule) {
            $this->executeBusinessRuleForModelOperationWithExecutionContext($context, $modelName, $operation, $businessRule, $model, $options);
        }
        foreach ($this->getModelOperationBusinessRules('*', '*') as $businessRule) {
            $this->executeBusinessRuleForModelOperationWithExecutionContext($context, $modelName, $operation, $businessRule, $model, $options);
        }

        return $this;
    }
    /**
     * @param string $id
     * @param array  $params
     * @param array  $options
     *
     * @return $this
     */
    public function executeBusinessRuleById($id, array $params = [], array $options = [])
    {
        $businessRule = $this->getBusinessRuleById($id);

        try {
            call_user_func_array($businessRule['callable'], array_merge($params, [$businessRule['params'], $options]));
        } catch (Exception\BusinessRuleException $e) {
            throw new Exception\NamedBusinessRuleException($businessRule['id'], $businessRule['name'], $e->getData(), $e);
        } catch (\Exception $e) {
            throw new Exception\NamedBusinessRuleException($businessRule['id'], $businessRule['name'], [], $e);
        }

        return $this;
    }
    /**
     * @param string $modelName
     * @param string $operation
     * @param array  $businessRule
     * @param mixed  $model
     * @param array  $options
     *
     * @return $this
     */
    protected function executeBusinessRuleForModelOperation($modelName, $operation, array $businessRule, $model, array $options = [])
    {
        try {
            call_user_func_array($businessRule['callable'], [$model, $operation, $modelName, $businessRule['params'], $options, $this->getContextService()]);
        } catch (Exception\BusinessRuleException $e) {
            throw new Exception\NamedBusinessRuleException($businessRule['id'], $businessRule['name'], $e->getData() + ['subType' => $e->getSubType(), 'model' => $modelName, 'operation' => $operation], $e);
        } catch (\Exception $e) {
            throw new Exception\NamedBusinessRuleException($businessRule['id'], $businessRule['name'], ['subType' => null, 'model' => $modelName, 'operation' => $operation], $e);
        }

        return $this;
    }
    /**
     * @param ValidationContextInterface $context
     * @param string                     $modelName
     * @param string                     $operation
     * @param array                      $businessRule
     * @param mixed                      $model
     * @param array                      $options
     *
     * @return $this
     */
    protected function executeBusinessRuleForModelOperationWithExecutionContext(ValidationContextInterface $context, $modelName, $operation, array $businessRule, $model, array $options = [])
    {
        try {
            call_user_func_array($businessRule['callable'], [$model, $context, $operation, $modelName, $businessRule['params'], $options, $this->getContextService()]);
        } catch (Exception\BusinessRuleException $e) {
            throw new Exception\NamedBusinessRuleException($businessRule['id'], $businessRule['name'], $e->getData() + ['subType' => $e->getSubType(), 'model' => $modelName, 'operation' => $operation], $e);
        } catch (\Exception $e) {
            throw new Exception\NamedBusinessRuleException($businessRule['id'], $businessRule['name'], ['subType' => null, 'model' => $modelName, 'operation' => $operation], $e);
        }

        return $this;
    }
}
