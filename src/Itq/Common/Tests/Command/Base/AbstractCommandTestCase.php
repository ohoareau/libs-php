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

        return parent::o();
    }
}
