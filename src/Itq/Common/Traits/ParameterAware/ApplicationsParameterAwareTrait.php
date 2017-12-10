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

use Exception;

/**
 * Applications Parameter Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ApplicationsParameterAwareTrait
{
    /**
     * @param array $applications
     *
     * @return $this
     */
    public function setApplications($applications)
    {
        return $this->setParameter('applications', (array) $applications);
    }
    /**
     * @return array
     *
     * @throws Exception
     */
    public function getApplications()
    {
        return $this->getArrayParameter('applications');
    }
    /**
     * @param string $name
     *
     * @return array
     *
     * @throws Exception
     */
    public function getApplication($name)
    {
        return $this->getArrayParameterKey('applications', $name);
    }
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    abstract protected function setParameter($key, $value);
    /**
     * @param string $name
     * @param string $key
     *
     * @return mixed
     *
     * @throws Exception
     */
    abstract protected function getArrayParameterKey($name, $key);
    /**
     * @param string $name
     *
     * @return array
     *
     * @throws Exception
     */
    abstract protected function getArrayParameter($name);
}
