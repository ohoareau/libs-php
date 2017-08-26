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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NoMoreUnitException extends UsageDeniedException
{
    /**
     * @var array
     */
    protected $context;
    /**
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     * @param array          $context
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
