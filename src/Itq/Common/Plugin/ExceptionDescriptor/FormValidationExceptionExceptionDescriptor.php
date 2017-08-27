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
use Itq\Common\Exception\FormValidationException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FormValidationExceptionExceptionDescriptor extends Base\AbstractExceptionDescriptor
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
        list ($code, $data) = parent::build($exception);

        /** @var FormValidationException $exception */

        $data['type']   = 'form';
        $data['errors'] = $exception->getErrors();

        return [$code, $data];
    }
}
