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

use Itq\Common\Service\ArchiverService;

/**
 * ArchiverServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ArchiverServiceAwareTrait
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
     * @return ArchiverService
     */
    public function getArchiverService()
    {
        return $this->getService('archiver');
    }
    /**
     * @param ArchiverService $service
     *
     * @return $this
     */
    public function setArchiverService(ArchiverService $service)
    {
        return $this->setService('archiver', $service);
    }
}
