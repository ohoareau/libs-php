<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Twig\Base;

use Itq\Common\Traits;

use Twig_Extension;
use Twig_Extension_GlobalsInterface;

/**
 * @author itiQiti Dev Team <cto@itiqiti.com>
 */
abstract class AbstractExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    use Traits\ServiceTrait;
}
