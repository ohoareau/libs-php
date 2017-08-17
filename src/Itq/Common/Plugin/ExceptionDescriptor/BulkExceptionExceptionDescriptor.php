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
use Itq\Common\Exception\BulkException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BulkExceptionExceptionDescriptor extends Base\AbstractExceptionDescriptor
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof BulkException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var BulkException $exception */
        $code                   = 412;
        $data                   = [];
        $data['type']           = 'bulk';
        $data['errorCount']     = $exception->getErrorCount();
        $data['errorData']      = $exception->getErrorData();
        $data['exceptionCount'] = $exception->getExceptionCount();
        $data['successCount']   = $exception->getSuccessCount();
        $data['successData']    = $exception->getSuccessData();
        $data['errors']         = [];
        foreach ($exception->getExceptions() as $index => $exception) {
            $data['errors'][$index] = $this->describe($exception);
        }

        return [$code, $data];
    }
}
