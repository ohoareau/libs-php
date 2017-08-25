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
use Itq\Common\Command\SdkGenerateCommand;
use Itq\Common\Tests\Command\Base\AbstractCommandTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group commands
 * @group commands/sdk-generate
 */
class SdkGenerateCommandTest extends AbstractCommandTestCase
{
    /**
     * @return SdkGenerateCommand
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @param array|null $with
     * @param array      $args
     * @param Exception  $exception
     *
     * @group unit
     *
     * @dataProvider getRunData
     */
    public function testRun($with, array $args, $exception = null)
    {
        $this->c()->setSdkService($this->mockedSdkService());

        if (null === $with) {
            $this->mockedSdkService()->expects($this->never())->method('generate');
        } else {
            call_user_func_array(
                [
                    $this->mockedSdkService()->expects($this->once())->method('generate'),
                    'with',
                ],
                $with
            );
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
            'missing-target-and-path' => [['php', 'sdk'], []],
            'missing-target'          => [['php', 'thepath'], ['path' => 'thepath']],
            'has-target-and-path'     => [['thetarget', 'thepath'], ['path' => 'thepath', 'target' => 'thetarget']],
        ];
    }
}
