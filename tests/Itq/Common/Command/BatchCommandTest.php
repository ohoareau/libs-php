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

use Exception;
use RuntimeException;
use Itq\Common\Command\BatchCommand;
use Itq\Common\Tests\Command\Base\AbstractCommandTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group commands
 * @group commands/batch
 */
class BatchCommandTest extends AbstractCommandTestCase
{
    /**
     * @return BatchCommand
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @param bool      $called
     * @param array     $with
     * @param array     $will
     * @param array     $args
     * @param bool|null $enabled
     * @param Exception $exception
     *
     * @group unit
     *
     * @dataProvider getRunData
     */
    public function testRun($called, $with, $will, $args, $enabled, Exception $exception = null)
    {
        $this->c()->setDispatchService($this->mockedDispatchService());

        if (null !== $enabled) {
            $this->c()->setEnabled($enabled);
        }
        if (true === $called) {
            call_user_func_array(
                [
                    $this->mockedDispatchService()->expects($this->once())->method('execute')->willReturn($will),
                    'with',
                ],
                $with
            );
        } else {
            $this->mockedDispatchService()->expects($this->never())->method('execute');
        }

        if (null !== $exception) {
            $this->expectExceptionThrown($exception);
        }

        $this->runCommand($args);
    }
    /**
     * @return array
     */
    public function getRunData()
    {
        return [
            'missing-name' => [false, null, null, [], true, new RuntimeException('Not enough arguments (missing: "name").', 0)],
            'disabled'     => [false, null, null, ['name' => 'batch1'], false],
            'enabled'      => [true, ['batchs.batch1'], null, ['name' => 'batch1'], true],
            'default'      => [false, null, null, ['name' => 'batch1'], null],
        ];
    }
}
