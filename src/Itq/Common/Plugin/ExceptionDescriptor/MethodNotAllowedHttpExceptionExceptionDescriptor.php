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

use Itq\Common\Plugin\ExceptionDescriptorInterface;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MethodNotAllowedHttpExceptionExceptionDescriptor implements ExceptionDescriptorInterface
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
        /** @var MethodNotAllowedHttpException $exception */
        $code            = 403;
        $data            = [];
        $data['code']    = 403;
        $data['message'] = 'Method not allowed on resource';

        return [$code, $data];
    }
}
