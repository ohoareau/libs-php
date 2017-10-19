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

use RuntimeException;
use Itq\Common\WorkflowInterface;
use Itq\Common\Service\WorkflowService;
use Itq\Common\WorkflowExecutorInterface;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/workflow
 */
class WorkflowServiceTest extends AbstractServiceTestCase
{
    /**
     * @return WorkflowService
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
        return [$this->mocked('executor', WorkflowExecutorInterface::class, ['executeModelOperation'])];
    }
    /**
     * @group unit
     */
    public function testRegisterFromDefinition()
    {
        $this->assertFalse($this->s()->has('workflow1'));
        $this->s()->registerFromDefinition('workflow1', []);
        $this->assertTrue($this->s()->has('workflow1'));

        $workflow = $this->s()->get('workflow1');

        $this->assertTrue($workflow instanceof WorkflowInterface);
    }
    /**
     * @group unit
     */
    public function testRegisterForExistingIdThrowException()
    {
        $this->assertFalse($this->s()->has('workflow1'));
        $this->s()->registerFromDefinition('workflow1', []);
        $this->assertTrue($this->s()->has('workflow1'));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Workflow 'workflow1' already exist");
        $this->expectExceptionCode(412);
        $this->s()->registerFromDefinition('workflow1', []);
    }
    /**
     * @group unit
     */
    public function testHasTransition()
    {
        $this->s()->registerFromDefinition('w', ['steps' => ['s1', 's2', 's3'], 'transitions' => ['s1' => ['s2'], 's2' => ['s3', 's1']]]);

        $this->assertTrue($this->s()->hasTransition('w', 's1', 's2'));
        $this->assertFalse($this->s()->hasTransition('w', 's1', 's3'));
        $this->assertFalse($this->s()->hasTransition('w', 's1', 'sX'));
        $this->assertTrue($this->s()->hasTransition('w', 's2', 's3'));
        $this->assertTrue($this->s()->hasTransition('w', 's2', 's1'));
        $this->assertFalse($this->s()->hasTransition('w', 's3', 's1'));
    }

    /**
     * @param array  $mocks
     * @param string $stepBefore
     * @param string $stepAfter
     * @param string $id
     * @param array  $definition
     *
     * @group unit
     * @dataProvider getTransitionModelPropertyData
     */
    public function testTransitionModelProperty($mocks, $stepBefore, $stepAfter, $id, $definition)
    {
        call_user_func_array(
            [$this->mocked('executor')->expects($this->exactly(count($mocks)))->method('executeModelOperation'), 'withConsecutive'],
            $mocks
        );

        $this->s()->registerFromDefinition($id, $definition);

        $docBefore = (object) ['status' => $stepBefore];
        $doc       = (object) ['status' => $stepAfter];

        $this->assertEquals(
            ['status.s1.leaved', 'status.s2.entered', 'status.s2.completed'],
            $this->s()->transitionModelProperty('m', $doc, 'status', $docBefore, $id)
        );
    }
    /**
     * @return array
     */
    public function getTransitionModelPropertyData()
    {
        return [
            '0 - basic allowed transition' => [
                [
                    ['m', 'status.s1.leaved', (object) ['status' => 's1'], []],
                    ['m', 'status.s2.entered', (object) ['status' => 's2'], []],
                    ['m', 'status.s2.completed', (object) ['status' => 's2'], []],
                ],
                's1',
                's2',
                'w',
                ['steps' => ['s1', 's2', 's3'], 'transitions' => ['s1' => ['s2'], 's2' => ['s3', 's1']]],
            ],
            '1 - transition alias' => [
                [
                    ['m', 'status.s1.leaved', (object) ['status' => 's1'], []],
                    ['m', 'status.s2.entered', (object) ['status' => 's2'], []],
                    ['m', 'status.s2.completed', (object) ['status' => 's2'], []],
                    ['m', 'a', (object) ['status' => 's2'], ['old' => (object) ['status' => 's1']]],
                ],
                's1',
                's2',
                'w',
                ['transitionAliases' => ['a' => 's1->s2'], 'steps' => ['s1', 's2', 's3'], 'transitions' => ['s1' => ['s2'], 's2' => ['s3', 's1']]],
            ],
            '2 - transition alias with variable' => [
                [
                    ['m', 'status.s1.leaved', (object) ['status' => 's1'], []],
                    ['m', 'status.s2.entered', (object) ['status' => 's2'], []],
                    ['m', 'status.s2.completed', (object) ['status' => 's2'], []],
                    ['m', 'a.s1', (object) ['status' => 's2'], ['old' => (object) ['status' => 's1']]],
                ],
                's1',
                's2',
                'w',
                ['transitionAliases' => ['a.{status}' => 's1->s2'], 'steps' => ['s1', 's2', 's3'], 'transitions' => ['s1' => ['s2'], 's2' => ['s3', 's1']]],
            ],
        ];
    }
    /**
     * @param string           $id
     * @param string           $currentStep
     * @param string           $targetStep
     * @param array            $definition
     * @param RuntimeException $expectedException
     *
     * @group unit
     * @dataProvider provideCheckTransitionExistExceptionData
     */
    public function testCheckTransitionExistExceptions($id, $currentStep, $targetStep, $definition, $expectedException)
    {
        $this->s()->registerFromDefinition('w', $definition);
        $this->expectExceptionThrown($expectedException);
        $this->s()->checkTransitionExist($id, $currentStep, $targetStep);
    }
    /**
     * @return array
     */
    public function provideCheckTransitionExistExceptionData()
    {
        $definition1 = ['steps' => ['s1', 's2', 's3'], 'transitions' => ['s1' => ['s2'], 's2' => ['s3', 's1']]];

        return [
            ['w', 's1', 's3', $definition1, new RuntimeException('Transitionning to s3 is not allowed', 412)],
            ['w', 's1', 's1', $definition1, new RuntimeException('Already s1', 412)],
        ];
    }
}
