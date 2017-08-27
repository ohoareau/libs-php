<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Thrower;

use Exception;
use RuntimeException;
use Itq\Common\ErrorManagerInterface;

/**
 * BaseThrower trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait BaseThrowerTrait
{
    /**
     * @param int    $code
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    protected function createExceptionArray($code, $msg, array $params)
    {
        if (method_exists($this, 'hasErrorManager') && $this->hasErrorManager() && method_exists($this, 'getErrorManager')) {
            /** @var ErrorManagerInterface $errorManager */
            $errorManager = $this->getErrorManager();

            return $errorManager->createException($msg, $params, ['exceptionCode' => $code]);
        }

        return new RuntimeException(call_user_func_array('sprintf', array_merge([$msg], $params)), $code);
    }
    /**
     * @param int    $code
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    protected function createException($code, $msg, ...$params)
    {
        return $this->createExceptionArray($code, $msg, $params);
    }
}
