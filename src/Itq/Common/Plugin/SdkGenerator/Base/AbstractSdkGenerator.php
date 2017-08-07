<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\SdkGenerator\Base;

use Itq\Common\Traits;
use Itq\Common\Plugin\SdkGeneratorInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractSdkGenerator implements SdkGeneratorInterface
{
    use Traits\ServiceTrait;
}
