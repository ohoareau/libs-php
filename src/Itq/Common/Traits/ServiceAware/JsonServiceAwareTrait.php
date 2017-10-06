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

use Itq\Common\Service\JsonService;

/**
 * JsonServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait JsonServiceAwareTrait
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
     * @return JsonService
     */
    public function getJsonService()
    {
        return $this->getService('jsonService');
    }
    /**
     * @param JsonService $service
     *
     * @return $this
     */
    public function setJsonService(JsonService $service)
    {
        return $this->setService('jsonService', $service);
    }
}
