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

use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\ExceptionDescriptorInterface;
use Itq\Common\Exception\UnsupportedAccountTypeException;

use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class UnsupportedAccountTypeExceptionExceptionDescriptor extends AbstractPlugin implements ExceptionDescriptorInterface
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof UnsupportedAccountTypeException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var UnsupportedAccountTypeException $exception */
        $code         = 403;
        $data         = [];
        $data['code'] = 403;

        return [$code, $data];
    }
}
