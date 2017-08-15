<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Event;

use Itq\Common\Tests\Event\Base\AbstractEventTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group events
 * @group events/database-query
 */
class DatabaseQueryEventTest extends AbstractEventTestCase
{
    /**
     * @return array
     */
    public function constructor()
    {
        return ['type', [], [], 0, 1, []];
    }
}
