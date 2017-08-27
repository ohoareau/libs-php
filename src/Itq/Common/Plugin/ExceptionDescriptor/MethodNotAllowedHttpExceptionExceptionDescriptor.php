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
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MethodNotAllowedHttpExceptionExceptionDescriptor extends Base\AbstractExceptionDescriptor
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof MethodNotAllowedHttpException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        list ($code, $data) = $this->build($exception, 403, 403);

        /** @var MethodNotAllowedHttpException $exception */

        $data['message'] = 'Method not allowed on resource';

        return [$code, $data];
    }
}
