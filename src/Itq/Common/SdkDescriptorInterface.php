<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface SdkDescriptorInterface
{
    /**
     * @return string
     */
    public function getTargetPath();
    /**
     * @param string $targetPath
     *
     * @return $this
     */
    public function setTargetPath($targetPath);
    /**
     * @return string
     */
    public function getTargetName();
    /**
     * @param string $targetName
     *
     * @return $this
     */
    public function setTargetName($targetName);
    /**
     * @return array
     */
    public function getParams();
    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params);
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function getParam($key, $default = null);
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParam($key, $value);
}
