<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

/**
 * Model Property Authorization Checker Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelPropertyAuthorizationCheckerInterface
{
    /**
     * @param mixed  $doc
     * @param string $property
     * @param string $operation
     * @param array  $options
     *
     * @return bool
     */
    public function isAllowed($doc, $property, $operation, array $options = []);
}
