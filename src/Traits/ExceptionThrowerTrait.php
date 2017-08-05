<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use RuntimeException;

/**
 * ExceptionThrower trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ExceptionThrowerTrait
{
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createDeniedException($msg, ...$params)
    {
        return $this->createExceptionArray(403, $msg, $params);
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createNotFoundException($msg, ...$params)
    {
        return $this->createExceptionArray(404, $msg, $params);
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createMalformedException($msg, ...$params)
    {
        return $this->createExceptionArray(412, $msg, $params);
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createFailedException($msg, ...$params)
    {
        return $this->createExceptionArray(500, $msg, $params);
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createRequiredException($msg, ...$params)
    {
        return $this->createExceptionArray(412, $msg, $params);
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createNotYetImplementedException($msg, ...$params)
    {
        return $this->createExceptionArray(500, 'Feature not yet implemented: '.$msg, $params);
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createAuthorizationRequiredException($msg, ...$params)
    {
        return $this->createExceptionArray(401, $msg, $params);
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createUnexpectedException($msg, ...$params)
    {
        return $this->createExceptionArray(500, $msg, $params);
    }
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createDuplicatedException($msg, ...$params)
    {
        return $this->createExceptionArray(412, $msg, $params);
    }
    /**
     * @param int    $code
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createExceptionArray($code, $msg, array $params)
    {
        return new RuntimeException(call_user_func_array('sprintf', array_merge([$msg], $params)), $code);
    }
    /**
     * @param int    $code
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected function createException($code, $msg, ...$params)
    {
        return $this->createExceptionArray($code, $msg, $params);
    }
}
