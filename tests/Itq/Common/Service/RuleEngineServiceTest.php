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

use Itq\Common\Service;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

use RuntimeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/rule-engine
 */
class RuleEngineServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\RuleEngineService
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
            $this->mockedCallableService(),
        ];
    }
    /**
     *
     */
    public function initializer()
    {
        $this->mock('templating', EngineInterface::class);
        $this->mock('expressionService', new Service\ExpressionService($this->mockedTemplating(), new ExpressionLanguage()));
    }
    /**
     * @group integ
     */
    public function testComputeForUnknownRuleTypeThrowException()
    {
        $this->s()->setCallableService(new Service\CallableService());
        $this->s()->registerRuleType(
            'someExistingRuleType',
            function () {
                return 0.1;
            }
        );

        $data = [];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("No 'unknownRuleType' in ruleTypes list");
        $this->expectExceptionCode(412);

        $this->s()->compute($data, [['type' => 'unknownRuleType']]);
    }
}
