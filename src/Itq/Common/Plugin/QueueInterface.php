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
interface QueueInterface
{
    /**
     * @return mixed
     */
    public function unqueue();
    /**
     * @param mixed $item
     */
    public function queue($item);
    /**
     * @return bool
     */
    public function isEmpty();
    /**
     * @return void
     */
    public function optimize();
    /**
     * @return void
     */
    public function clear();
    /**
     * @return array
     */
    public function all();
}
