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

use Itq\Common\Service\DataProviderService;

/**
 * DataProviderServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DataProviderServiceAwareTrait
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
     * @return DataProviderService
     */
    public function getDataProviderService()
    {
        return $this->getService('dataProviderService');
    }
    /**
     * @param DataProviderService $service
     *
     * @return $this
     */
    public function setDataProviderService(DataProviderService $service)
    {
        return $this->setService('dataProviderService', $service);
    }
}
