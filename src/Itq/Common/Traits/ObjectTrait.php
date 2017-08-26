<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use ReflectionClass;

/**
 * Object trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ObjectTrait
{
    /**
     * @param object $object
     *
     * @return string
     */
    public function getClass($object = null)
    {
        return get_class($object ?: $this);
    }
    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getClassNamespace($object = null)
    {
        return str_replace('/', '\\', dirname(str_replace('\\', '/', $this->getClass($object))));
    }
    /**
     * @param object $object
     *
     * @return string
     */
    public function getClassShortName($object = null)
    {
        return basename(str_replace('\\', '/', $this->getClass($object)));
    }
    /**
     * @param object $object
     *
     * @return string
     */
    public function getClassFile($object = null)
    {
        return $this->getClassReflection($object)->getFileName();
    }
    /**
     * @param object $object
     *
     * @return string
     */
    public function getClassDirectory($object = null)
    {
        return dirname($this->getClassFile($object));
    }
    /**
     * @param object $object
     *
     * @return ReflectionClass
     */
    public function getClassReflection($object = null)
    {
        return new ReflectionClass($object ?: $this);
    }
    /**
     * @param object $object
     *
     * @return array
     */
    public function toArray($object = null)
    {
        return get_object_vars($object ?: $this);
    }
}
