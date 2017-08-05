<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DocumentEvent extends Event
{
    /**
     * @var object|array
     */
    protected $data;
    /**
     * @var array
     */
    protected $context;
    /**
     * @param object|array $data
     * @param array        $context
     */
    public function __construct($data, array $context = [])
    {
        $this->setData($data);
        $this->setContext($context);
    }
    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
    /**
     * @param string     $key
     * @param null|mixed $defaultValue
     *
     * @return mixed
     */
    public function getContextVariable($key, $defaultValue = null)
    {
        if (!isset($this->context[$key])) {
            return $defaultValue;
        }

        return $this->context[$key];
    }
    /**
     * @param array|object $data
     *
     * @return $this
     */
    protected function setData($data)
    {
        $this->data = $data;

        return $this;
    }
    /**
     * @param array $context
     *
     * @return DocumentEvent
     */
    protected function setContext($context)
    {
        $this->context = $context;

        return $this;
    }
}
