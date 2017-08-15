<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Form\TypeGuesser\Base;

use Itq\Common\Traits;

use Symfony\Component\Form\FormTypeGuesserInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTypeGuesser implements FormTypeGuesserInterface
{
    use Traits\ServiceTrait;
}
