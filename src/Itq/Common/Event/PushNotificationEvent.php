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

use Symfony\Component\EventDispatcher\Event;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PushNotificationEvent extends Event
{
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $content;
    /**
     * @var array
     */
    protected $recipients;
    /**
     * @var array
     */
    protected $what;
    /**
     * @var array
     */
    protected $options;
    /**
     * @param string $title
     * @param string $content
     * @param array  $recipients
     * @param array  $what
     * @param array  $options
     */
    public function __construct($title, $content, $recipients, $what, array $options = [])
    {
        $this->setTitle($title);
        $this->setContent($content);
        $this->setRecipients($recipients);
        $this->setWhat($what);
        $this->setOptions($options);
    }
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }
    /**
     * @return array
     */
    public function getWhat()
    {
        return $this->what;
    }
    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    /**
     * @param string $title
     *
     * @return $this
     */
    protected function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
    /**
     * @param string $content
     *
     * @return $this
     */
    protected function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
    /**
     * @param array $recipients
     *
     * @return $this
     */
    protected function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }
    /**
     * @param array $what
     *
     * @return $this
     */
    protected function setWhat(array $what)
    {
        $this->what = $what;

        return $this;
    }
    /**
     * @param array $options
     *
     * @return $this
     */
    protected function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }
}
