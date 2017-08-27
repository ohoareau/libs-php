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
interface ModelPropertyAuthorizationCheckerAwareInterface
{
    /**
     * @param Plugin\ModelPropertyAuthorizationCheckerInterface $propertyAuthorizationChecker
     *
     * @return $this
     */
    public function addModelPropertyAuthorizationChecker(Plugin\ModelPropertyAuthorizationCheckerInterface $propertyAuthorizationChecker);
    /**
     * @return Plugin\ModelPropertyAuthorizationCheckerInterface[]
     */
    public function getModelPropertyAuthorizationCheckers();
}
