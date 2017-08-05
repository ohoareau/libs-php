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

use Itq\Common\Service\FilterService;

/**
 * FilterServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait FilterServiceAwareTrait
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
     * @return FilterService
     */
    public function getFilterService()
    {
        return $this->getService('filter');
    }
    /**
     * @param FilterService $service
     *
     * @return $this
     */
    public function setFilterService(FilterService $service)
    {
        return $this->setService('filter', $service);
    }
}
