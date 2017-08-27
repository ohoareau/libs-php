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
class PushNotificationEvent extends Base\AbstractTextNotificationEvent
{
    /**
     * @var string
     */
    protected $title;
    /**
     * @var array
     */
    protected $what;
    /**
     * @param string $title
     * @param string $content
     * @param array  $recipients
     * @param array  $what
     * @param array  $options
     */
    public function __construct($title, $content, $recipients, $what, array $options = [])
    {
        parent::__construct($content, $recipients, [], [], null, $options);
        $this->setTitle($title);
        $this->setWhat($what);
    }
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * @return array
     */
    public function getWhat()
    {
        return $this->what;
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
     * @param array $what
     *
     * @return $this
     */
    protected function setWhat(array $what)
    {
        $this->what = $what;

        return $this;
    }
}
