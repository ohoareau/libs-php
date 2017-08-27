<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

/**
 * ExceptionThrower trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ExceptionThrowerTrait
{
    use Thrower\BaseThrowerTrait;
    use Thrower\FailedExceptionThrowerTrait;
    use Thrower\DeniedExceptionThrowerTrait;
    use Thrower\NotFoundExceptionThrowerTrait;
    use Thrower\RequiredExceptionThrowerTrait;
    use Thrower\MalformedExceptionThrowerTrait;
    use Thrower\UnexpectedExceptionThrowerTrait;
    use Thrower\DuplicatedExceptionThrowerTrait;
    use Thrower\NotYetImplementedExceptionThrowerTrait;
    use Thrower\AuthorizationRequiredExceptionThrowerTrait;
}
