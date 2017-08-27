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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NotFoundHttpExceptionExceptionDescriptor extends Base\AbstractExceptionDescriptor
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
        list ($code, $data) = parent::build($exception, 404, 404);

        /** @var NotFoundHttpException $exception */

        $data['message'] = 'Resource not found';

        return [$code, $data];
    }
}
