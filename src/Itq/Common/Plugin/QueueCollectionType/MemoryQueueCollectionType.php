<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\QueueCollectionType;

use Itq\Common\Plugin\QueueCollectionInterface;
use Itq\Common\Plugin\QueueCollection\MemoryQueueCollection;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryQueueCollectionType extends Base\AbstractCollectionQueueType
{
    /**
     * @param array $definition
     * @param array $options
     *
     * @return QueueCollectionInterface
     */
    public function create(array $definition = [], array $options = [])
    {
        unset($definition, $options);

        return new MemoryQueueCollection();
    }
}
