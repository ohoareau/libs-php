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

use RuntimeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BulkException extends RuntimeException
{
    /**
     * @var array
     */
    protected $successData;
    /**
     * @var array
     */
    protected $errorData;
    /**
     * @var int
     */
    protected $successCount;
    /**
     * @var int
     */
    protected $errorCount;
    /**
     * @var \Exception[]
     */
    protected $exceptions;
    /**
     * @var int
     */
    protected $exceptionCount;
    /**
     * @param \Exception[] $exceptions
     * @param array        $errorData
     * @param array        $successData
     */
    public function __construct(array $exceptions, array $errorData = [], array $successData = [])
    {
        parent::__construct('Bulk exception', 412);

        $this->exceptions     = $exceptions;
        $this->exceptionCount = count($exceptions);
        $this->successData    = $successData;
        $this->errorData      = $errorData;
        $this->successCount   = count($successData);
        $this->errorCount     = count($errorData);
    }
    /**
     * @return array
     */
    public function getSuccessData()
    {
        return $this->successData;
    }
    /**
     * @return array
     */
    public function getErrorData()
    {
        return $this->errorData;
    }
    /**
     * @return int
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }
    /**
     * @return int
     */
    public function getErrorCount()
    {
        return $this->errorCount;
    }
    /**
     * @return \Exception[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }
    /**
     * @return int
     */
    public function getExceptionCount()
    {
        return $this->exceptionCount;
    }
}
