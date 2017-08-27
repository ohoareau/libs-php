<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelPropertyAuthorizationCheckerService extends Base\AbstractModelPropertyAuthorizationCheckerService
{
    /**
     * @param object $doc
     * @param string $property
     * @param string $operation
     * @param array  $options
     *
     * @return bool
     */
    public function isPropertyOperationAllowed($doc, $property, $operation, array $options = [])
    {
        $result = true;

        foreach ($this->getModelPropertyAuthorizationCheckers() as $propertyAuthorizatonChecker) {
            if (!$propertyAuthorizatonChecker->isAllowed($doc, $property, $operation, $options)) {
                $result = false;
            }
        }

        return $result;
    }
}
