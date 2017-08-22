<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Security\Base;

use Itq\Common\Traits;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken as BaseToken;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractToken extends BaseToken
{
    use Traits\BaseTrait;
}
