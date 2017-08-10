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

/**
 * MissingMethodCatcher trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait MissingMethodCatcherTrait
{
    /**
     * @param int    $code
     * @param string $msg
     * @param array  $params
     *
     * @return \Exception
     */
    protected abstract function createException($code, $msg, ...$params);
    /**
     * @param string $name
     * @param array  $args
     *
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        throw $this->createExceptionArray(500, '#1001:service.method.unknown', [get_class($this), $name]);
    }
}