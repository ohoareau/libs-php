<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Command;

use Itq\Common\Command\BusinessRuleListCommand;
use Symfony\Component\Console\Output\BufferedOutput;
use Itq\Common\Tests\Command\Base\AbstractCommandTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group commands
 * @group commands/business-rule-list
 */
class BusinessRuleListCommandTest extends AbstractCommandTestCase
{
    /**
     * @return BusinessRuleListCommand
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @param string $expectedText
     *
     * @group unit
     *
     * @dataProvider getRunData
     */
    public function testRun($expectedText, $data)
    {
        $this->c()->setBusinessRuleService($this->mockedBusinessRuleService());
        $this->mockedBusinessRuleService()->expects($this->once())->method('getFlattenBusinessRuleDefinitions')->willReturn($data);

        list ($result, $input, $output) = $this->runCommand();

        unset($result, $input);

        if (null !== $expectedText) {
            /** @var BufferedOutput $output */
            $text = $output->fetch();

            $this->assertEquals(rtrim(join(PHP_EOL, $expectedText)), rtrim($text));
        }
    }
    /**
     * @return array
     */
    public function getRunData()
    {
        return [
            [
                [],
                [],
            ],
            [
                [
                    ' br1 on op1 model1 name1 ',
                ],
                [
                    ['id' => 'br1', 'operation' => 'op1', 'model' => 'model1', 'name' => 'name1', 'tenants' => [], 'notTenants' => []],
                ],
            ],
            [
                [
                    ' br2 on op2 model2 name2',
                    ' br3 on op.number3 model number3 name3 (only for tenant: TENANT2, TENANT3)',
                    ' br4 on op.number4 model number4 name4 (not for tenant: TENANT4)',
                ],
                [
                    ['id' => 'br2', 'operation' => 'op2', 'model' => 'model2', 'name' => 'name2', 'tenants' => [], 'notTenants' => []],
                    ['id' => 'br3', 'operation' => 'op.number3', 'model' => 'model.number3', 'name' => 'name3', 'tenants' => ['tenant2', 'tenant3'], 'notTenants' => []],
                    ['id' => 'br4', 'operation' => 'op.number4', 'model' => 'model.number4', 'name' => 'name4', 'tenants' => [], 'notTenants' => ['tenant4']],
                ],
            ],
        ];
    }
}
