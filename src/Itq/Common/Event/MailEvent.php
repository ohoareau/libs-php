<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Event;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MailEvent extends Base\AbstractTextNotificationEvent
{
    /**
     * @var string
     */
    protected $subject;
    /**
     * @param string $subject
     * @param string $content
     * @param array  $recipients
     * @param array  $attachments
     * @param array  $images
     * @param mixed  $sender
     * @param array  $options
     */
    public function __construct($subject, $content, $recipients, array $attachments = [], array $images = [], $sender = null, array $options = [])
    {
        parent::__construct($content, $recipients, $attachments, $images, $sender, $options);
        $this->setSubject($subject);
    }
    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
    /**
     * @param string $subject
     *
     * @return $this
     */
    protected function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }
}
