<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * Vault Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class VaultService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\StorageServiceAwareTrait;
    /**
     * @param Service\StorageService $storageService
     */
    public function __construct(Service\StorageService $storageService)
    {
        $this->setStorageService($storageService);
    }
    /**
     * @param string $key
     * @param string $value
     * @param array  $options
     *
     * @return $this
     */
    public function savePassword($key, $value, $options = [])
    {
        unset($options);

        $this->getStorageService()->save('/registry/passwords/'.md5($key), $value);

        return $this;
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return string
     */
    public function retrievePassword($key, $options = [])
    {
        unset($options);

        return $this->getStorageService()->read('/registry/passwords/'.md5($key));
    }
}
