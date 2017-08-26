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

use Itq\Common\Event\MailEvent;
use Itq\Common\Tests\Event\Base\AbstractEventTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group events
 * @group events/mail
 */
class MailEventTest extends AbstractEventTestCase
{
    /**
     * @return MailEvent
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
        return ['subject', 'content', [], [], [], null, []];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('subject', $this->e()->getSubject());
        $this->assertEquals('content', $this->e()->getContent());
        $this->assertEquals([], $this->e()->getRecipients());
        $this->assertEquals([], $this->e()->getAttachments());
        $this->assertEquals([], $this->e()->getImages());
        $this->assertEquals(null, $this->e()->getSender());
        $this->assertEquals([], $this->e()->getOptions());
    }
}
