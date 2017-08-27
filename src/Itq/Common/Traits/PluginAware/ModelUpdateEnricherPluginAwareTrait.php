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
 * ModelUpdateEnricher Plugin Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelUpdateEnricherPluginAwareTrait
{
    /**
     * @param string                              $type
     * @param Plugin\ModelUpdateEnricherInterface $updateEnricher
     *
     * @return $this
     */
    public function addModelUpdateEnricher($type, Plugin\ModelUpdateEnricherInterface $updateEnricher)
    {
        return $this->setArrayParameterKey('modelUpdateEnrichers', $type, $updateEnricher);
    }
    /**
     * @return Plugin\ModelUpdateEnricherInterface[]
     */
    public function getModelUpdateEnrichers()
    {
        return $this->getArrayParameter('modelUpdateEnrichers');
    }
    /**
     * @param string $type
     *
     * @return Plugin\ModelUpdateEnricherInterface
     */
    public function getModelUpdateEnricher($type)
    {
        return $this->getArrayParameterKey('modelUpdateEnrichers', $type);
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
    /**
     * @param string $name
     * @param string $key
     *
     * @return mixed
     */
    abstract protected function getArrayParameterKey($name, $key);
}
