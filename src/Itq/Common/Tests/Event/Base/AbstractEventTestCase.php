<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Event\Base;

use Itq\Common\Tests\Base\AbstractTestCase;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractEventTestCase extends AbstractTestCase
{
    /**
     * @return Event
     */
    public function e()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
}
