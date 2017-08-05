<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\YamlService;

/**
 * YamlServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait YamlServiceAwareTrait
{
    /**
     * @param string $key
     * @param mixed  $service
     *
     * @return $this
     */
    protected abstract function setService($key, $service);
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getService($key);
    /**
     * @return YamlService
     */
    public function getYamlService()
    {
        return $this->getService('yaml');
    }
    /**
     * @param YamlService $service
     *
     * @return $this
     */
    public function setYamlService(YamlService $service)
    {
        return $this->setService('yaml', $service);
    }
}
