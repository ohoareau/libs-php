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

/**
 * Tenant Aware Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface TenantAwareInterface
{
    /**
     * @return string
     */
    public function getTenant();
}
