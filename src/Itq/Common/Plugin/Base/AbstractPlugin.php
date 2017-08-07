<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Base;

use Itq\Common\Traits;
use Itq\Common\PluginInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractPlugin implements PluginInterface
{
    use Traits\ServiceTrait;
}
