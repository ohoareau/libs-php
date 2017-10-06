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

use Itq\Common\Service\MetaDataService;

/**
 * MetaDataServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait MetaDataServiceAwareTrait
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
     * @param MetaDataService $service
     *
     * @return $this
     */
    public function setMetaDataService(MetaDataService $service)
    {
        return $this->setService('metaDataService', $service);
    }
    /**
     * @return MetaDataService
     */
    public function getMetaDataService()
    {
        return $this->getService('metaDataService');
    }
}
