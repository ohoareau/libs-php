<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RuleEngineService
{
    use Traits\ServiceTrait;
    use Traits\CallableBagTrait;
    /**
     * Register a rule type for the specified name (replace if exist).
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function registerRuleType($name, $callable, array $options = [])
    {
        return $this->registerCallableByType('ruleType', $name, $callable, $options);
    }
    /**
     * Return the rule type registered for the specified name.
     *
     * @param string $name
     *
     * @return callable
     *
     * @throws \Exception if no converter registered for this name
     */
    public function getRuleType($name)
    {
        return $this->getCallableByType('ruleType', $name);
    }
    /**
     * @param array $data
     * @param array $rules
     * @param array $options
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function compute(array &$data, array $rules, array $options = [])
    {
        foreach ($rules as $id => $rule) {
            $result = $this->computeRule($data, $rule, $options);

            if (null !== $result) {
                return [$result, isset($rule['id']) ? $rule['id'] : $id];
            }
        }

        return [null, null];
    }
    /**
     * @param array $data
     * @param array $rule
     * @param array $options
     *
     * @return mixed|null
     */
    public function computeRule(array &$data, array $rule, array $options = [])
    {
        $rule += ['type' => 'unknown'];

        $name = $rule['type'];

        unset($rule['type']);

        return $this->executeCallableByType('ruleType', $name, [$data, $rule, $options]);
    }
}
