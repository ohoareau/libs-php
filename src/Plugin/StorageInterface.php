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
 * Storage Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface StorageInterface
{
    /**
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     */
    public function set($key, $value, $options = []);
    /**
     * @param string $key
     * @param array  $options
     *
     * @return $this
     */
    public function clear($key, $options = []);
    /**
     * @param string $key
     * @param array  $options
     *
     * @return mixed
     */
    public function get($key, $options = []);
    /**
     * @param string $key
     * @param array  $options
     *
     * @return mixed
     */
    public function has($key, $options = []);
}
