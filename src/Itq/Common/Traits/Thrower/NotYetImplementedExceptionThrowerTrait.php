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

/**
 * NotYetImplementedExceptionThrower trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait NotYetImplementedExceptionThrowerTrait
{
    /**
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    protected function createNotYetImplementedException($msg, ...$params)
    {
        return $this->createExceptionArray(500, 'Feature not yet implemented: '.$msg, $params);
    }
    /**
     * @param int    $code
     * @param string $msg
     * @param array  $params
     *
     * @return Exception
     */
    abstract protected function createExceptionArray($code, $msg, array $params);
}
