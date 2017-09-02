<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Helper\Collection;

/**
 * Collection trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CollectionTrait
{
    /**
     * @param mixed $value
     *
     * @return $this
     */
    protected function ensureIsArray(&$value)
    {
        if (!is_array($value)) {
            $value = [];
        }

        return $this;
    }
    /**
     * @param array  $array
     * @param string $key
     *
     * @return $this
     */
    protected function ensureArrayKeyIsArray(array &$array, $key)
    {
        if (!$this->isArrayKeyAnArray($array, $key)) {
            $array[$key] = [];
        }

        return $this;
    }
    /**
     * @param array  $array
     * @param string $key
     *
     * @return bool
     */
    protected function isArrayKeyAnArray(array &$array, $key)
    {
        return isset($array[$key]) && is_array($array[$key]);
    }
    /**
     * @param array|mixed $array
     *
     * @return bool
     */
    protected function isArray(&$array)
    {
        return true === is_array($array);
    }
}
