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

use Itq\Common\Service\DateService;

/**
 * DateServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DateServiceAwareTrait
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
     * @return DateService
     */
    public function getDateService()
    {
        return $this->getService('date');
    }
    /**
     * @param DateService $service
     *
     * @return $this
     */
    public function setDateService(DateService $service)
    {
        return $this->setService('date', $service);
    }
}
