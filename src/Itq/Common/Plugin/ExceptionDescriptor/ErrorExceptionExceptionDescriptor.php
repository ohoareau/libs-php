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
use Itq\Common\Exception\ErrorException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ErrorExceptionExceptionDescriptor extends Base\AbstractExceptionDescriptor
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof ErrorException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var ErrorException $exception */
        $code = $exception->getCode();
        $data = [];
        $data['code']                = $exception->getCode();
        $data['message']             = $exception->getMessage();
        $data['type']                = 'error';
        $data['applicationCode']     = $exception->getApplicationCode();
        $data['applicationKey']      = $exception->getApplicationKey();
        $data['applicationParams']   = $exception->getApplicationParams();
        $data['applicationMetaData'] = $exception->getApplicationMetaData();
        $data['exceptionType']       = str_replace("\\", '.', get_class($exception));
        $data['shortExceptionType']  = preg_replace('/Exception$/', '', lcfirst(basename(str_replace('\\', '/', get_class($exception)))));

        return [$code, $data];
    }
}
