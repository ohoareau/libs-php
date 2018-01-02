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
 * UserClass Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait UserClassParameterAwareTrait
{
    /**
     * @return string
     */
    public function getUserClass()
    {
        return $this->getParameter('userClass');
    }
    /**
     * @param string $userClass
     *
     * @return $this
     */
    public function setUserClass($userClass)
    {
        return $this->setParameter('userClass', $userClass);
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
