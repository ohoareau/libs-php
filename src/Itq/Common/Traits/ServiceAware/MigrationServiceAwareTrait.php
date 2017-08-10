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

use Itq\Common\Service\MigrationService;

/**
 * MigrationServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait MigrationServiceAwareTrait
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
     * @param MigrationService $service
     *
     * @return $this
     */
    public function setMigrationService(MigrationService $service)
    {
        return $this->setService('migration', $service);
    }
    /**
     * @return MigrationService
     */
    public function getMigrationService()
    {
        return $this->getService('migration');
    }
}
