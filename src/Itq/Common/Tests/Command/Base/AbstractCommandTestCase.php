<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Command\Base;

use Itq\Common\Tests\Base\AbstractTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractCommandTestCase extends AbstractTestCase
{
    /**
     * @return Command
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
    /**
     * @param array $args
     *
     * @return array
     */
    protected function runCommand(array $args = [])
    {
        $input  = new ArrayInput($args);
        $output = new BufferedOutput();
        $result = $this->c()->run($input, $output);

        return [$result, $input, $output];
    }
}
