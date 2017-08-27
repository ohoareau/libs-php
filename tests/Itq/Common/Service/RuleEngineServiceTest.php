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
     *
     */
    public function initializer()
    {
        $this->mockedTemplating();
        $this->mocked('expressionService', new Service\ExpressionService($this->mockedTemplateService(), new ExpressionLanguage()));
    }
    /**
     * @group integ
     */
    public function testComputeForUnknownRuleTypeThrowException()
    {
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
