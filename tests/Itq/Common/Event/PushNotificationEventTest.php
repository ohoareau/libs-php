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

use Itq\Common\Event\PushNotificationEvent;
use Itq\Common\Tests\Event\Base\AbstractEventTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group events
 * @group events/push-notification
 */
class PushNotificationEventTest extends AbstractEventTestCase
{
    /**
     * @return PushNotificationEvent
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
        return ['title', 'content', [], [], []];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('title', $this->e()->getTitle());
        $this->assertEquals('content', $this->e()->getContent());
        $this->assertEquals([], $this->e()->getRecipients());
        $this->assertEquals([], $this->e()->getWhat());
        $this->assertEquals([], $this->e()->getOptions());
    }
}
