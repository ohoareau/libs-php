<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ExceptionDescriptor\Base;

use Exception;
use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\ExceptionDescriptorInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractExceptionDescriptor extends AbstractPlugin implements ExceptionDescriptorInterface
{
    /**
     * @param Exception $exception
     * @param int|null  $statusCode
     * @param int|null  $code
     *
     * @return array
     */
    protected function build(Exception $exception, $statusCode = null, $code = null)
    {
        $computedStatusCode = $exception->getCode() > 0 ? $exception->getCode() : 500;

        return [$statusCode ?: $computedStatusCode, ['code' => $code ?: $exception->getCode()]];
    }
}
