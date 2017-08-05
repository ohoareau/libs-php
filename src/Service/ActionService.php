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

use Itq\Common\Bag;
use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * Action Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ActionService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\CallableServiceAwareTrait;
    use Traits\ServiceAware\ExpressionServiceAwareTrait;
    /**
     * @param Service\CallableService   $callableService
     * @param Service\ExpressionService $expressionService
     */
    public function __construct(Service\CallableService $callableService, Service\ExpressionService $expressionService)
    {
        $this->setCallableService($callableService);
        $this->setExpressionService($expressionService);
    }
    /**
     * Register an action for the specified name (replace if exist).
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($name, $callable, array $options = [])
    {
        $this->getCallableService()->registerByType('action', $name, $callable, $options);

        return $this;
    }
    /**
     * Register an action set for the specified name (replace if exist).
     *
     * @param string $name
     * @param array  $actions
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function registerSet($name, array $actions, array $options = [])
    {
        $this->getCallableService()->registerSetByType('action', $name, $actions, $options);

        return $this;
    }
    /**
     * Return the action registered for the specified name.
     *
     * @param string $name
     *
     * @return callable
     *
     * @throws \Exception if no action registered for this name
     */
    public function get($name)
    {
        return $this->getCallableService()->getByType('action', $name);
    }
    /**
     * @param string $name
     * @param Bag    $params
     * @param Bag    $context
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function execute($name, Bag $params, Bag $context)
    {
        return $this->getCallableService()->executeByType('action', $name, [$params, $context]);
    }
    /**
     * @param array $actions
     * @param Bag   $params
     * @param Bag   $context
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function executeBulk(array $actions, Bag $params, Bag $context)
    {
        $that = $this;

        return $this->getCallableService()->executeListByType(
            'action',
            $actions,
            function ($callableParams, $callableOptions, $preCompute = false) use ($params, $context, $that) {
                $p = clone $params;
                $p->setDefault($callableParams);
                $vars = $p->all() + $context->all();

                $context->set($callableOptions);

                $_params = $p->all();

                if ($preCompute) {
                    $_params = array_intersect_key($_params, ['if' => true, 'ifNot' => true]);
                }

                return [new Bag($that->getExpressionService()->evaluate($_params, $vars)), $context];
            },
            function ($rawParams) use ($that) {
                list($params) = $rawParams;
                /** @var Bag $params */
                if ($params->has('if') && !$that->isExpressionTrue($params->get('if'))) {
                    return false;
                }
                if ($params->has('ifNot') && $that->isExpressionTrue($params->get('ifNot'))) {
                    return false;
                }

                return true;
            }
        );
    }
    /**
     * @param string $expression
     *
     * @return bool
     */
    public function isExpressionTrue($expression)
    {
        return true === $expression || (0 !== $expression && null !== $expression && false !== $expression && (!is_string($expression) || (strlen($expression) > 0)));
    }
}
