<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BadTimeTokenException extends AuthenticationException
{
    /**
     * Construct the exception
     */
    public function __construct()
    {
        parent::__construct('Time re-authentication required', 401);
    }
}
