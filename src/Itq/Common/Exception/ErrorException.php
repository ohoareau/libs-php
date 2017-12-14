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
class ErrorException extends RuntimeException
{
    /**
     * @var int
     */
    protected $applicationCode;
    /**
     * @var string
     */
    protected $applicationKey;
    /**
     * @var array
     */
    protected $applicationParams;
    /**
     * @var array
     */
    protected $applicationMetaData;
    /**
     * @param string         $message
     * @param null|int       $code
     * @param string|null    $applicationKey
     * @param array          $applicationParams
     * @param null|int       $applicationCode
     * @param array          $applicationMetaData
     * @param Exception|null $previousException
     */
    public function __construct(
        $message,
        $code = null,
        $applicationKey = null,
        array $applicationParams = [],
        $applicationCode = 0,
        array $applicationMetaData = [],
        Exception $previousException = null
    ) {
        parent::__construct($message, $code, $previousException);

        $this->applicationKey      = $applicationKey;
        $this->applicationCode     = $applicationCode;
        $this->applicationParams   = $applicationParams;
        $this->applicationMetaData = $applicationMetaData;
    }
    /**
     * @return int
     */
    public function getApplicationCode()
    {
        return $this->applicationCode;
    }
    /**
     * @return string|null
     */
    public function getApplicationKey()
    {
        return $this->applicationKey;
    }
    /**
     * @return array
     */
    public function getApplicationParams()
    {
        return $this->applicationParams;
    }
    /**
     * @return array
     */
    public function getApplicationMetaData()
    {
        return $this->applicationMetaData;
    }
}
