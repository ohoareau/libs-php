<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ParameterAware;

/**
 * Default Senders Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DefaultSendersParameterAwareTrait
{
    /**
     * @param array $defaultSenders
     *
     * @return $this
     */
    public function setDefaultSenders(array $defaultSenders)
    {
        return $this->setParameter('defaultSenders', $defaultSenders);
    }
    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getDefaultSenders()
    {
        return $this->getParameter('defaultSenders');
    }
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    abstract protected function setParameter($key, $value);
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    abstract protected function getParameter($key, $default = null);
}
