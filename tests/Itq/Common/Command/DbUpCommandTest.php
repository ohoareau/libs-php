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
use Itq\Common\Command\DbUpCommand;
use Itq\Common\Tests\Command\Base\AbstractCommandTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group commands
 * @group commands/db-up
 */
class DbUpCommandTest extends AbstractCommandTestCase
{
    /**
     * @return DbUpCommand
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @param bool      $called
     * @param array     $will
     * @param array     $args
     * @param bool|null $master
     * @param Exception $exception
     *
     * @group unit
     *
     * @dataProvider getRunData
     */
    public function testRun($called, $will, $args, $master, Exception $exception = null)
    {
        $this->c()->setMigrationService($this->mockedMigrationService());

        if (null !== $master) {
            $this->c()->setMaster($master);
        }
        if (true === $called) {
            $this->mockedMigrationService()->expects($this->once())->method('upgrade')->willReturn($will);
        } else {
            $this->mockedMigrationService()->expects($this->never())->method('upgrade');
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
            'not-master' => [false, null, [], false],
            'master'     => [true, null, [], true],
            'default'    => [false, null, [], null],
        ];
    }
}
