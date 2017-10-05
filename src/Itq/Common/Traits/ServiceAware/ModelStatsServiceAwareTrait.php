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

use Itq\Common\Service\ModelStatsService;

/**
 * ModelStatsServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelStatsServiceAwareTrait
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
     * @return ModelStatsService
     */
    public function getModelStatsService()
    {
        return $this->getService('modelStatsService');
    }
    /**
     * @param ModelStatsService $service
     *
     * @return $this
     */
    public function setModelStatsService(ModelStatsService $service)
    {
        return $this->setService('modelStatsService', $service);
    }
}
