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

use RuntimeException;
use Itq\Common\Event\DatabaseQueryEvent;
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
     * @return DatabaseQueryEvent
     */
    public function e()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::e();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return ['type', [], [], 0, 1, []];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('type', $this->e()->getType());
        $this->assertEquals([], $this->e()->getQuery());
        $this->assertEquals([], $this->e()->getParams());
        $this->assertEquals(0, $this->e()->getStartTime());
        $this->assertEquals(1, $this->e()->getEndTime());
        $this->assertEquals([], $this->e()->getResult());
        $this->assertEquals(null, $this->e()->getException());

        $exception = new RuntimeException('This is an exception', 500);

        $this->e()->setException($exception);

        $this->assertEquals($exception, $this->e()->getException());
    }
}
