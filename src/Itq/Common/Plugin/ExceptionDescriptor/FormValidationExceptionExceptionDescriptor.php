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

use Itq\Common\Exception\FormValidationException;
use Itq\Common\Plugin\ExceptionDescriptorInterface;

use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FormValidationExceptionExceptionDescriptor implements ExceptionDescriptorInterface
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof FormValidationException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var FormValidationException $exception */
        $code           = 412;
        $data           = [];
        $data['type']   = 'form';
        $data['errors'] = $exception->getErrors();

        return [$code, $data];
    }
}
