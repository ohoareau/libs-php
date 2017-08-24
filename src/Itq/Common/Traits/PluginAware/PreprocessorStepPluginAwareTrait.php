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
 * PreprocessorStep Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PreprocessorStepPluginAwareTrait
{
    /**
     * @param string                           $name
     * @param Plugin\PreprocessorStepInterface $step
     *
     * @return $this
     */
    public function addPreprocessorStep($name, Plugin\PreprocessorStepInterface $step)
    {
        return $this->setArrayParameterKey('preprocessorSteps', $name, $step);
    }
    /**
     * @return Plugin\PreprocessorStepInterface[]
     */
    public function getPreprocessorSteps()
    {
        return $this->getArrayParameter('preprocessorSteps');
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
