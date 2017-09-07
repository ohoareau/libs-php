<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Plugin\QueueCollectionInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class QueueCollectionService
{
    use Traits\ServiceTrait;
    use Traits\PluginAware\QueueCollectionTypePluginAwareTrait;
    /**
     * @param string $type
     * @param array  $definition
     * @param array  $options
     *
     * @return QueueCollectionInterface
     */
    public function create($type, array $definition = [], array $options = [])
    {
        return $this->getQueueCollectionType($type)->create($definition, $options);
    }
}
