<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\QueueCollection;

use Exception;
use RuntimeException;
use Itq\Common\Plugin\QueueInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryQueueCollection extends Base\AbstractQueueCollection
{
    /**
     * @var QueueInterface[]
     */
    protected $queues;
    /**
     *
     */
    public function __construct()
    {
        $this->queues = [];
    }
    /**
     * @param string $queueName
     *
     * @return mixed
     */
    public function unqueue($queueName)
    {
        return $this->getQueue($queueName)->unqueue();
    }
    /**
     * @param string $queueName
     *
     * @return QueueInterface
     *
     * @throws Exception
     */
    public function getQueue($queueName)
    {
        $this->checkQueueExist($queueName);

        return $this->queues[$queueName];
    }
    /**
     * @param string $queueName
     *
     * @return $this
     */
    public function checkQueueExist($queueName)
    {
        if (!$this->hasQueue($queueName)) {
            throw new RuntimeException(sprintf("Unknown queue '%s'", $queueName), 412);
        }

        return $this;
    }
    /**
     * @param string $queueName
     *
     * @return bool
     */
    public function hasQueue($queueName)
    {
        return true === isset($this->queues[$queueName]);
    }
    /**
     * @param string $queueName
     * @param mixed  $item
     */
    public function queue($queueName, $item)
    {
        $this->getQueue($queueName)->queue($item);
    }
    /**
     * @return bool
     */
    public function isEmpty()
    {
        foreach ($this->getQueues() as $queue) {
            if (!$queue->isEmpty()) {
                return false;
            }
        }

        return true;
    }
    /**
     * @return QueueInterface[]
     */
    public function getQueues()
    {
        return $this->queues;
    }
    /**
     * @param array|null $queueNames
     *
     * @return bool[]
     */
    public function areEmpty(array $queueNames)
    {
        $statuses = [];

        foreach ($queueNames as $queueName) {
            $statuses[$queueName] = $this->getQueue($queueName)->isEmpty();
        }

        return $statuses;
    }
    /**
     * @param array|null $queueNames
     *
     * @return void
     */
    public function optimize($queueNames = null)
    {
        foreach ($this->getSelectedQueues($queueNames) as $queue) {
            $queue->optimize();
        }
    }
    /**
     * @return string[]
     */
    public function getQueueNames()
    {
        return array_keys($this->queues);
    }
    /**
     * @param string[]|null $queueNames
     *
     * @return array
     */
    public function getSelectedQueues($queueNames)
    {
        $queues = [];

        foreach (is_array($queueNames) ? $queueNames : $this->getQueueNames() as $queueName) {
            $queues[$queueName] = $this->getQueue($queueName);
        }

        return $queues;
    }
    /**
     * @param array|null $queueNames
     *
     * @return void
     */
    public function clear($queueNames = null)
    {
        foreach ($this->getSelectedQueues($queueNames) as $queueName => $queue) {
            $queue->clear();
            unset($this->queues[$queueNames]);
        }
    }
    /**
     * @param array|null $queueNames
     *
     * @return array
     */
    public function getItems($queueNames = null)
    {
        $items = [];

        foreach ($this->getSelectedQueues($queueNames) as $queueName => $queue) {
            $items[$queueName] = $queue->all();
        }

        return $items;
    }
}
