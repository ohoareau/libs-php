<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * Workflow.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
class Workflow implements WorkflowInterface
{
    /**
     * @var array
     */
    protected $steps;
    /**
     * @var array
     */
    protected $transitions;
    /**
     * @var array
     */
    protected $transitionAliases;
    /**
     * @var array
     */
    protected $requiredFields;
    /**
     * @param array $definition
     */
    public function __construct(array $definition = [])
    {
        $definition += ['steps' => [], 'transitions' => [], 'transitionAliases' => [], 'requiredFields' => []];

        if (!is_array($definition['steps'])) {
            $definition['steps'] = [];
        }

        if (!is_array($definition['transitions'])) {
            $definition['transitions'] = [];
        }

        foreach ($definition['steps'] as $stepName) {
            $this->addStep($stepName);
        }

        foreach ($definition['transitions'] as $stepName => $transitions) {
            if (!is_array($transitions)) {
                $transitions = [];
            }
            foreach ($transitions as $targetStep) {
                $this->addTransition($stepName, $targetStep);
            }
        }

        if (!is_array($definition['transitionAliases'])) {
            $definition['transitionAliases'] = [];
        }

        foreach ($definition['transitionAliases'] as $alias => $transition) {
            $this->addTransitionAlias($alias, $transition);
        }

        if (!is_array($definition['requiredFields'])) {
            $definition['requiredFields'] = [];
        }

        $this->setRequiredFields($definition['requiredFields']);
    }
    /**
     * @param array $requiredFields
     *
     * @return $this
     */
    public function setRequiredFields(array $requiredFields)
    {
        $this->requiredFields = $requiredFields;

        return $this;
    }
    /**
     * @return array
     */
    public function getRequiredFields()
    {
        return $this->requiredFields;
    }
    /**
     * @param string $currentStep
     * @param string $targetStep
     *
     * @return bool
     */
    public function hasTransition($currentStep, $targetStep)
    {
        return isset($this->transitions[$currentStep][$targetStep]);
    }
    /**
     * @param string $name
     *
     * @return $this
     */
    public function addStep($name)
    {
        $this->steps[$name] = [];

        return $this;
    }
    /**
     * @param string $from
     * @param string $to
     *
     * @return $this
     */
    public function addTransition($from, $to)
    {
        $this->checkStepExist($from);
        $this->checkStepExist($to);

        if (!isset($this->transitions[$from])) {
            $this->transitions[$from] = [];
        }

        $this->transitions[$from][$to] = [];

        return $this;
    }
    /**
     * @param string $alias
     * @param string $transition
     *
     * @return $this
     */
    public function addTransitionAlias($alias, $transition)
    {
        if (!isset($this->transitionAliases[$transition])) {
            $this->transitionAliases[$transition] = [];
        }

        $this->transitionAliases[$transition][] = $alias;

        return $this;
    }
    /**
     * @param string $transition
     *
     * @return array
     */
    public function getTransitionAliases($transition)
    {
        if (!isset($this->transitionAliases[$transition])) {
            return [];
        }

        return $this->transitionAliases[$transition];
    }
    /**
     * @param string $step
     *
     * @return $this
     */
    public function checkStepExist($step)
    {
        if (!$this->hasStep($step)) {
            throw new \RuntimeException(sprintf("Unknown step '%s'", $step), 500);
        }

        return $this;
    }
    /**
     * @param string $step
     *
     * @return bool
     */
    public function hasStep($step)
    {
        return isset($this->steps[$step]);
    }
}
