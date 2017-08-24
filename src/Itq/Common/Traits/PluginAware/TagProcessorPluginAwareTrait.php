<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\PluginAware;

use Itq\Common\Plugin;

/**
 * TagProcessor Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait TagProcessorPluginAwareTrait
{
    /**
     * @param Plugin\TagProcessorInterface $tagProcessor
     *
     * @return $this
     */
    public function addTagProcessor(Plugin\TagProcessorInterface $tagProcessor)
    {
        return $this->pushArrayParameterKeyItem('tagProcessors', $tagProcessor->getTag(), $tagProcessor);
    }
    /**
     * @return array
     */
    public function getTagProcessors()
    {
        return $this->getArrayParameter('tagProcessors');
    }
    /**
     * @param string $name
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    abstract protected function setArrayParameterKey($name, $key, $value);
    /**
     * @param string $name
     *
     * @return array
     */
    abstract protected function getArrayParameter($name);
}
