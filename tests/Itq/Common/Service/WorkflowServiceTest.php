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
        return [$this->mock('executor', WorkflowExecutorInterface::class, ['executeModelOperation'])];
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
     * @group unit
     */
    public function testTransition()
    {
        $this->mock('executor')->expects($this->exactly(3))->method('executeModelOperation');

        $this->s()->registerFromDefinition('w', ['steps' => ['s1', 's2', 's3'], 'transitions' => ['s1' => ['s2'], 's2' => ['s3', 's1']]]);

        $docBefore = new \stdClass();
        $docBefore->status = 's1';

        $doc = new \stdClass();
        $doc->status = 's2';

        $this->s()->transitionModelProperty('m', $doc, 'status', $docBefore, 'w');
    }
}
