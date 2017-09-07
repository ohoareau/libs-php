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
use Itq\Common\Command\DocGenerateCommand;
use Itq\Common\Tests\Command\Base\AbstractCommandTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group commands
 * @group commands/doc-generate
 */
class DocGenerateCommandTest extends AbstractCommandTestCase
{
    /**
     * @return DocGenerateCommand
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
        $this->c()->setDocService($this->mockedDocService());

        if (null === $with) {
            $this->mockedDocService()->expects($this->never())->method('generate');
        } else {
            call_user_func_array(
                [
                    $this->mockedDocService()->expects($this->once())->method('generate'),
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
            'missing-type-and-path' => [['default', 'doc'], []],
            'missing-type'          => [['default', 'thepath'], ['path' => 'thepath']],
            'has-type-and-path'     => [['thetype', 'thepath'], ['path' => 'thepath', 'type' => 'thetype']],
        ];

    }
}
