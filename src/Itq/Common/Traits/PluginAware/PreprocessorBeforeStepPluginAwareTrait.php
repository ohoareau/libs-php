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
 * PreprocessorBeforeStepPlugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PreprocessorBeforeStepPluginAwareTrait
{
    /**
     * @param string                                 $name
     * @param Plugin\PreprocessorBeforeStepInterface $step
     *
     * @return $this
     */
    public function addPreprocessorBeforeStep($name, Plugin\PreprocessorBeforeStepInterface $step)
    {
        return $this->setArrayParameterKey('preprocessorBeforeSteps', $name, $step);
    }
    /**
     * @return Plugin\PreprocessorBeforeStepInterface[]
     */
    public function getPreprocessorBeforeSteps()
    {
        return $this->getArrayParameter('preprocessorBeforeSteps');
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
