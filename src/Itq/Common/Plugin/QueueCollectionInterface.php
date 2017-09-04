<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface QueueCollectionInterface
{
    /**
     * @param string $queueName
     *
     * @return mixed
     */
    public function unqueue($queueName);
    /**
     * @param string $queueName
     * @param mixed  $item
     */
    public function queue($queueName, $item);
    /**
     * @return bool
     */
    public function isEmpty();
    /**
     * @param array|null $queueNames
     *
     * @return bool[]
     */
    public function areEmpty(array $queueNames);
    /**
     * @param array|null $queueNames
     *
     * @return void
     */
    public function optimize($queueNames = null);
    /**
     * @param array|null $queueNames
     *
     * @return void
     */
    public function clear($queueNames = null);
    /**
     * @param array|null $queueNames
     *
     * @return array
     */
    public function getItems($queueNames = null);
}
