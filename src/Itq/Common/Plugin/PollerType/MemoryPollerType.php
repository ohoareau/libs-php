<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PollerType;

use Itq\Common\Plugin\PollerInterface;
use Itq\Common\Plugin\Poller\MemoryPoller;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryPollerType extends Base\AbstractPollerType
{
    /**
     * @param array $definition
     * @param array $options
     *
     * @return PollerInterface
     */
    public function create(array $definition = [], array $options = [])
    {
        unset($options);

        return new MemoryPoller($definition);
    }
}
