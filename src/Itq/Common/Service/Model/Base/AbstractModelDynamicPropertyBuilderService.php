<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model\Base;

use Itq\Common\Traits;
use Itq\Common\Service\Model\ModelDynamicPropertyBuilderServiceInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractModelDynamicPropertyBuilderService implements ModelDynamicPropertyBuilderServiceInterface
{
    use Traits\ServiceTrait;
    use Traits\PluginAware\ModelDynamicPropertyBuilderPluginAwareTrait;
}
