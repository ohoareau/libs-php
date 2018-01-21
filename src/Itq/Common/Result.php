<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class Result
{
    const STATUS_DONE = 0;
    const STATUS_QUEUED = 1;
    const STATUS_REFUSED = 2;
    /**
     * @var int
     */
    protected $status;
    /**
     * @var mixed
     */
    protected $data;
    /**
     * @param int        $status
     * @param null|mixed $data
     */
    public function __construct($status, $data = null)
    {
        $this->setStatus($status);
        $this->setData($data);
    }
    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @param int $status
     *
     * @return $this
     */
    protected function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
    /**
     * @param mixed $data
     *
     * @return $this
     */
    protected function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
