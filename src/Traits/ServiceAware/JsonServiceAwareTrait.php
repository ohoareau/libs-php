<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\JsonService;

/**
 * JsonServiceAware trait.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
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
        return $this->getService('json');
    }
    /**
     * @param JsonService $service
     *
     * @return $this
     */
    public function setJsonService(JsonService $service)
    {
        return $this->setService('json', $service);
    }
}
