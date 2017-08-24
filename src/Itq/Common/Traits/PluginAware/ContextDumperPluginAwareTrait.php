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
trait ContextDumperPluginAwareTrait
{
    /**
     * @param Plugin\ContextDumperInterface $contextDumper
     *
     * @return $this
     */
    public function addContextDumper(Plugin\ContextDumperInterface $contextDumper)
    {
        return $this->setArrayParameterKey('contextDumpers', uniqid('context-dumper'), $contextDumper);
    }
    /**
     * @return Plugin\ContextDumperInterface[]
     */
    public function getContextDumpers()
    {
        return $this->getArrayParameter('contextDumpers');
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
