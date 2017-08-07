<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ErrorManagerInterface
{
    /**
     * @param string $key
     * @param array  $params
     * @param array  $options
     *
     * @return \Exception
     */
    public function createException($key, array $params = [], array $options = []);
    /**
     * @param array $options
     *
     * @return array
     */
    public function getErrorCodes(array $options = []);
    /**
     * @param int   $code
     * @param array $options
     *
     * @return array
     */
    public function getErrorCode($code, array $options = []);
}
