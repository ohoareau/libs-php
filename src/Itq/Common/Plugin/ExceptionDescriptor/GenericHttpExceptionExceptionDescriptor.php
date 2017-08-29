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
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GenericHttpExceptionExceptionDescriptor extends Base\AbstractExceptionDescriptor
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof HttpExceptionInterface;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var HttpExceptionInterface $exception */

        return $this->build($exception, $exception->getStatusCode(), $exception->getStatusCode());
    }
}
