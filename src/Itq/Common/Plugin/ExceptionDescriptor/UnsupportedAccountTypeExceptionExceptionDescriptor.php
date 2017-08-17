<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ExceptionDescriptor;

use Exception;
use Itq\Common\Exception\UnsupportedAccountTypeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class UnsupportedAccountTypeExceptionExceptionDescriptor extends Base\AbstractExceptionDescriptor
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof UnsupportedAccountTypeException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var UnsupportedAccountTypeException $exception */
        $code         = 403;
        $data         = [];
        $data['code'] = 403;

        return [$code, $data];
    }
}
