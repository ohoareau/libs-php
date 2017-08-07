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
class UnsupportedTokenGeneratorTypeException extends AuthenticationException
{
    /**
     * Construct the exception
     *
     * @param string $type
     */
    public function __construct($type)
    {
        parent::__construct(sprintf("Unsupported token generator type '%s'", $type), 403);
    }
}
