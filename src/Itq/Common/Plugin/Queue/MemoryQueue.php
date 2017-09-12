<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Queue;

use Exception;
use RuntimeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryQueue extends Base\AbstractQueue
{
    /**
     * @var array
     */
    protected $items;
    /**
     * @return mixed
     *
     * @throws Exception if queue is empty
     */
    public function unqueue()
    {
        if ($this->isEmpty()) {
            throw new RuntimeException('Queue is empty', 412);
        }

        return array_shift($this->items);
    }
    /**
     * @param mixed $item
     */
    public function queue($item)
    {
        $this->items[] = $item;
    }
    /**
     * @return bool
     */
    public function isEmpty()
    {
        return 0 >= count($this->items);
    }
    /**
     * @return void
     */
    public function optimize()
    {
        $this->items = array_values($this->items);
    }
    /**
     * @return void
     */
    public function clear()
    {
        $this->items = [];
    }
    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }
}
