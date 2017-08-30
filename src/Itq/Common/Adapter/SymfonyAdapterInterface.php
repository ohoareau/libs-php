<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Adapter;

/**
 * Symfony Adapter Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface SymfonyAdapterInterface
{
    /**
     * @return string
     */
    public function getVersion();
    /**
     * @return int
     */
    public function getVersionId();
    /**
     * @return int
     */
    public function getMajorVersion();
    /**
     * @return int
     */
    public function getMinorVersion();
    /**
     * @return int
     */
    public function getReleaseVersion();
    /**
     * @return string
     */
    public function getExtraVersion();
    /**
     * @return string
     */
    public function getEndOfMaintenance();
    /**
     * @return string
     */
    public function getEndOfLife();
}
