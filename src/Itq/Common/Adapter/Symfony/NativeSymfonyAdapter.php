<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Adapter\Symfony;

use Symfony\Component\HttpKernel\Kernel;

/**
 * Native Symfony Adapter.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NativeSymfonyAdapter extends Base\AbstractSymfonyAdapter
{
    /**
     * @return string
     */
    public function getVersion()
    {
        return Kernel::VERSION;
    }
    /**
     * @return int
     */
    public function getVersionId()
    {
        return Kernel::VERSION_ID;
    }
    /**
     * @return int
     */
    public function getMajorVersion()
    {
        return Kernel::MAJOR_VERSION;
    }
    /**
     * @return int
     */
    public function getMinorVersion()
    {
        return Kernel::MINOR_VERSION;
    }
    /**
     * @return int
     */
    public function getReleaseVersion()
    {
        return Kernel::RELEASE_VERSION;
    }
    /**
     * @return string
     */
    public function getExtraVersion()
    {
        return kernel::EXTRA_VERSION;
    }
    /**
     * @return string
     */
    public function getEndOfMaintenance()
    {
        return Kernel::END_OF_MAINTENANCE;
    }
    /**
     * @return string
     */
    public function getEndOfLife()
    {
        return kernel::END_OF_LIFE;
    }
}
