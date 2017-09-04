<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Model\Internal\Result;

use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ExceptionResult extends Base\AbstractResult
{
    /**
     * @var Exception
     */
    protected $exception;
    /**
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        parent::__construct(
            [
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
                'status'  => 'exception',
                'type'    => get_class($exception),
            ],
            'exception'
        );

        $this->exception = $exception;
    }
    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
