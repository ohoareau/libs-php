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
use Itq\Common\Plugin\QueueCollectionTypeInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class QueueCollectionService
{
    use Traits\ServiceTrait;
    /**
     * @param string $type
     * @param array  $definition
     * @param array  $options
     *
     * @return QueueCollectionInterface
     */
    public function createQueueCollection($type, array $definition = [], array $options = [])
    {
        return $this->getQueueCollectionType($type)->create($definition, $options);
    }
    /**
     * @param string                       $type
     * @param QueueCollectionTypeInterface $queueCollectionType
     *
     * @return $this
     */
    public function addQueueCollectionType($type, QueueCollectionTypeInterface $queueCollectionType)
    {
        return $this->setArrayParameterKey('queueCollectionTypes', $type, $queueCollectionType);
    }
    /**
     * @return QueueCollectionTypeInterface[]
     */
    public function getQueueCollectionTypes()
    {
        return $this->getArrayParameter('queueCollectionTypes');
    }
    /**
     * @param string $type
     *
     * @return QueueCollectionTypeInterface
     */
    public function getQueueCollectionType($type)
    {
        return $this->getArrayParameterKey('queueCollectionTypes', $type);
    }
}
