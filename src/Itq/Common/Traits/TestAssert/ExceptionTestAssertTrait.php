<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\TestAssert;

use Exception;
use PHPUnit_Framework_Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ExceptionTestAssertTrait
{
    /**
     * @param string $exception
     */
    abstract public function expectException($exception);
    /**
     * @param int|string $code
     *
     * @throws PHPUnit_Framework_Exception
     */
    abstract public function expectExceptionCode($code);
    /**
     * @param string $message
     *
     * @throws PHPUnit_Framework_Exception
     */
    abstract public function expectExceptionMessage($message);
    /**
     * @param Exception $e
     *
     * @return $this
     */
    protected function expectExceptionThrown(Exception $e)
    {
        $this->expectException(get_class($e));
        $this->expectExceptionCode($e->getCode());
        $this->expectExceptionMessage($e->getMessage());

        return $this;
    }
}
