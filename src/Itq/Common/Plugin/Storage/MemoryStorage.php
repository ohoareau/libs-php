<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Storage;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryStorage extends Base\AbstractStorage
{
    /**
     * @param array $objects
     */
    public function __construct(array $objects = [])
    {
        $this->setParameter('objects', $objects);
    }
    /**
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     */
    public function set($key, $value, $options = [])
    {
        unset($options);

        return $this->setArrayParameterKey('objects', md5($key), $value);
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return $this
     */
    public function clear($key, $options = [])
    {
        unset($options);

        return $this->unsetArrayParameterKey('objects', md5($key));
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return string
     *
     * @throws \Exception
     */
    public function get($key, $options = [])
    {
        unset($options);

        return $this->getArrayParameterKey('objects', md5($key));
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return bool
     */
    public function has($key, $options = [])
    {
        unset($options);

        return $this->hasArrayParameterKey('objects', md5($key));
    }
}
