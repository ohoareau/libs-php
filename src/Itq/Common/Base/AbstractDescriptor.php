<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Base;

use Itq\Common\SdkDescriptorInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractDescriptor implements SdkDescriptorInterface
{
    /**
     * @var string
     */
    protected $targetPath;
    /**
     * @var string
     */
    protected $targetName;
    /**
     * @var array
     */
    protected $params;
    /**
     * @param string $targetName
     * @param string $targetPath
     * @param array  $params
     * @param array  $options
     */
    public function __construct($targetName, $targetPath, array $params = [], array $options = [])
    {
        $this->setTargetPath($targetPath);
        $this->setTargetName($targetName);
        $this->setParams($params);

        unset($options);
    }
    /**
     * @return string
     */
    public function getTargetPath()
    {
        return $this->targetPath;
    }
    /**
     * @param string $targetPath
     *
     * @return $this
     */
    public function setTargetPath($targetPath)
    {
        $this->targetPath = (string) $targetPath;

        return $this;
    }
    /**
     * @return string
     */
    public function getTargetName()
    {
        return $this->targetName;
    }
    /**
     * @param string $targetName
     *
     * @return $this
     */
    public function setTargetName($targetName)
    {
        $this->targetName = (string) $targetName;

        return $this;
    }
    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function getParam($key, $default = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }
}
