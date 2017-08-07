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

use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AuthenticationExceptionExceptionDescriptor extends AbstractPlugin implements ExceptionDescriptorInterface
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception)
    {
        return $exception instanceof AuthenticationException;
    }
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception)
    {
        /** @var AuthenticationException $exception */
        $code         = 401;
        $data         = [];
        $data['code'] = 401;

        return [$code, $data];
    }
}
