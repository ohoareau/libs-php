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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NotFoundHttpExceptionExceptionDescriptor implements ExceptionDescriptorInterface
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof NotFoundHttpException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var NotFoundHttpException $exception */
        $code            = 404;
        $data            = [];
        $data['code']    = 404;
        $data['message'] = 'Resource not found';

        return [$code, $data];
    }
}