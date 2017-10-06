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

use Itq\Common\Service\StorageService;

/**
 * StorageServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait StorageServiceAwareTrait
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
     * @param string $key
     *
     * @return bool
     */
    protected abstract function hasService($key);
    /**
     * @return StorageService
     */
    public function getStorageService()
    {
        return $this->getService('storageService');
    }
    /**
     * @return bool
     */
    public function hasStorageService()
    {
        return $this->hasService('storageService');
    }
    /**
     * @param StorageService $service
     *
     * @return $this
     */
    public function setStorageService(StorageService $service)
    {
        return $this->setService('storageService', $service);
    }
}
