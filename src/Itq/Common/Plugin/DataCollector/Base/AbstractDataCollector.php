<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataCollector\Base;

use Itq\Common\Traits;
use Itq\Common\PluginInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractDataCollector extends DataCollector implements PluginInterface
{
    use Traits\ServiceTrait;
    /**
     *
     */
    public function reset()
    {
    }
}
