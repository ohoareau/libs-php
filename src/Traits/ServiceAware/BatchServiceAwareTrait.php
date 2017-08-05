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

use Itq\Common\Service\BatchService;

/**
 * BatchServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait BatchServiceAwareTrait
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
     * @return BatchService
     */
    public function getBatchService()
    {
        return $this->getService('batch');
    }
    /**
     * @param BatchService $service
     *
     * @return $this
     */
    public function setBatchService(BatchService $service)
    {
        return $this->setService('batch', $service);
    }
}
