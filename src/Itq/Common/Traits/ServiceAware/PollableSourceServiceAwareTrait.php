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

use Itq\Common\Service\PollableSourceService;

/**
 * PollableSourceServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PollableSourceServiceAwareTrait
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
     * @return PollableSourceService
     */
    public function getPollableSourceService()
    {
        return $this->getService('pollableSourceService');
    }
    /**
     * @param PollableSourceService $service
     *
     * @return $this
     */
    public function setPollableSourceService(PollableSourceService $service)
    {
        return $this->setService('pollableSourceService', $service);
    }
}
