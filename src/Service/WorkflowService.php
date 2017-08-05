<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Workflow;
use Itq\Common\WorkflowInterface;
use Itq\Common\WorkflowExecutorInterface;

/**
 * Workflow Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class WorkflowService
{
    use Traits\ServiceTrait;
    /**
     * @param WorkflowExecutorInterface $executor
     */
    public function __construct(WorkflowExecutorInterface $executor)
    {
        $this->setExecutor($executor);
    }
    /**
     * @param WorkflowExecutorInterface $executor
     *
     * @return $this
     */
    public function setExecutor(WorkflowExecutorInterface $executor)
    {
        return $this->setService('executor', $executor);
    }
    /**
     * @return WorkflowExecutorInterface
     *
     * @throws \Exception
     */
    public function getExecutor()
    {
        return $this->getService('executor');
    }
    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return $this->hasArrayParameterKey('workflows', $id);
    }
    /**
     * @param string $id
     *
     * @return WorkflowInterface
     */
    public function get($id)
    {
        return $this->getArrayParameterKey('workflows', $id);
    }
    /**
     * @param string $id
     * @param array  $definition
     *
     * @return $this
     */
    public function registerFromDefinition($id, array $definition)
    {
        return $this->register($id, new Workflow($definition));
    }
    /**
     * @param string            $id
     * @param WorkflowInterface $workflow
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($id, WorkflowInterface $workflow)
    {
        if ($this->has($id)) {
            throw $this->createDuplicatedException("Workflow '%s' already exist", $id);
        }

        return $this->setArrayParameterKey('workflows', $id, $workflow);
    }
    /**
     * @param string $id
     * @param string $currentStep
     * @param string $targetStep
     *
     * @return bool
     */
    public function hasTransition($id, $currentStep, $targetStep)
    {
        $workflow = $this->get($id);

        return $workflow->hasTransition($currentStep, $targetStep);
    }
    /**
     * @param string $id
     * @param string $currentStep
     * @param string $targetStep
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkTransitionExist($id, $currentStep, $targetStep)
    {
        if (!$this->hasTransition($id, $currentStep, $targetStep)) {
            if ($currentStep === $targetStep) {
                throw $this->createRequiredException("Already %s", $targetStep);
            }

            throw $this->createRequiredException("Transitionning to %s is not allowed", $targetStep);
        }

        return $this;
    }
    /**
     * @param string $modelName
     * @param mixed  $model
     * @param string $property
     * @param mixed  $previousModel
     * @param string $id
     * @param array  $options
     *
     * @return array
     *
     * @throws \Exception
     */
    public function transitionModelProperty($modelName, $model, $property, $previousModel, $id, array $options = [])
    {
        $this->checkTransitionExist($id, $previousModel->$property, $model->$property);

        $this->getExecutor()->executeModelOperation($modelName, $property.'.'.$previousModel->$property.'.leaved', $previousModel, $options);
        $this->getExecutor()->executeModelOperation($modelName, $property.'.'.$model->$property.'.entered', $model, $options);
        $this->getExecutor()->executeModelOperation($modelName, $property.'.'.$model->$property.'.completed', $model, $options);

        $workflow = $this->get($id);

        foreach ($workflow->getTransitionAliases($previousModel->$property.'->'.$model->$property) as $alias) {
            $this->getExecutor()->executeModelOperation($modelName, $this->replaceVariables($alias, (array) $previousModel), $model, $options + ['old' => $previousModel]);
        }

        return [
            $property.'.'.$previousModel->$property.'.leaved',
            $property.'.'.$model->$property.'.entered',
            $property.'.'.$model->$property.'.completed',
        ];
    }
    /**
     * @param string $value
     * @param array  $vars
     *
     * @return string
     */
    protected function replaceVariables($value, $vars = [])
    {
        $matches = null;

        if (0 >= preg_match_all('/\{([^\}]+)\}/', $value, $matches)) {
            return $value;
        }

        foreach ($matches[1] as $i => $match) {
            $value = str_replace($matches[0][$i], isset($vars[$match]) ? $vars[$match] : null, $value);
        }

        return $value;
    }
}
