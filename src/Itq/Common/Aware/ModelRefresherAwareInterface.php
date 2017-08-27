<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware;

use Itq\Common\Plugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelRefresherAwareInterface
{
    /**
     * @param Plugin\ModelRefresherInterface $refresher
     *
     * @return $this
     */
    public function addModelRefresher(Plugin\ModelRefresherInterface $refresher);
    /**
     * @return Plugin\ModelRefresherInterface[]
     */
    public function getModelRefreshers();
}
