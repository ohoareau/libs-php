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
class DatabaseQueryEvent extends Event
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $query;
    /**
     * @var null|\Exception
     */
    protected $exception;
    /**
     * @var array
     */
    protected $params;
    /**
     * @var float
     */
    protected $startTime;
    /**
     * @var float
     */
    protected $endTime;
    /**
     * @var mixed
     */
    protected $result;
    /**
     * DatabaseQueryEvent constructor.
     *
     * @param string          $type
     * @param string          $query
     * @param array           $params
     * @param float           $startTime
     * @param float           $endTime
     * @param mixed           $result
     * @param \Exception|null $exception
     */
    public function __construct($type, $query, array $params, $startTime, $endTime, $result, \Exception $exception = null)
    {
        $this->setType($type);
        $this->setQuery($query);
        $this->setParams($params);
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
        $this->setResult($result);

        if (null !== $exception) {
            $this->setException($exception);
        }
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
    /**
     * @param string $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }
    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }
    /**
     * @param \Exception|null $exception
     *
     * @return $this
     */
    public function setException($exception)
    {
        $this->exception = $exception;

        return $this;
    }
    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }
    /**
     * @return float
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
    /**
     * @param float $date
     *
     * @return $this
     */
    public function setStartTime($date)
    {
        $this->startTime = $date;

        return $this;
    }
    /**
     * @return float
     */
    public function getEndTime()
    {
        return $this->endTime;
    }
    /**
     * @param float $date
     *
     * @return $this
     */
    public function setEndTime($date)
    {
        $this->endTime = $date;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
    /**
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }
}
