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
class BusinessRuleException extends RuntimeException
{
    /**
     * @var string
     */
    protected $subType;
    /**
     * @var array
     */
    protected $data;
    /**
     * @param string          $message
     * @param int             $code
     * @param null            $subType
     * @param array           $data
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $code = 0, $subType = null, $data = [], \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->subType = $subType;
        $this->data    = $data;
    }
    /**
     * @return string
     */
    public function getSubType()
    {
        return $this->subType;
    }
    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
