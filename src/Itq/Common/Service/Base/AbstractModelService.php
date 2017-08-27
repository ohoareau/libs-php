<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Base;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractModelService implements Service\ModelServiceInterface
{
    use Traits\ServiceTrait;
}
