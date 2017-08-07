<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Exception;

use Exception;
use RuntimeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NamedBusinessRuleException extends RuntimeException
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var \Exception
     */
    protected $exception;
    /**
     * @param string     $id
     * @param string     $name
     * @param array      $data
     * @param \Exception $previous
     */
    public function __construct($id, $name, $data, \Exception $previous)
    {
        parent::__construct(
            sprintf("Business rule #%s '%s' error: %s", $id, $name, $previous->getMessage()),
            $previous->getCode()
        );
        $this->data = $data;
        // do not set a "previous exception" because Symfony 2 Console is not behaving the right way
        $this->setBusinessRuleException($previous);
    }
    /**
     * @return \Exception
     */
    public function getBusinessRuleException()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->getPrevious();
    }
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    /**
     * @param string $id
     *
     * @return $this
     */
    protected function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    /**
     * @param string $name
     * @return $this
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    /**
     * @param Exception $e
     *
     * @return $this
     */
    protected function setBusinessRuleException(Exception $e)
    {
        $this->exception = $e;

        return $this;
    }
}
