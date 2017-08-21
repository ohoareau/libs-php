<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelPropertyAuthorizationChecker;

use Itq\Common\Traits;
use Itq\Common\Service;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SecureModelPropertyAuthorizationChecker extends Base\AbstractModelPropertyAuthorizationChecker
{
    use Traits\AuthorizationCheckerAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param Service\MetaDataService       $metaDataService
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(Service\MetaDataService $metaDataService, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->setMetaDataService($metaDataService);
        $this->setAuthorizationChecker($authorizationChecker);
    }
    /**
     * @param mixed  $doc
     * @param string $property
     * @param string $operation
     * @param array  $options
     *
     * @return bool
     */
    public function isAllowed($doc, $property, $operation, array $options = [])
    {
        $decision = null;

        foreach ($this->getMetaDataService()->getModelPropertySecures($doc, $property) as $secure) {
            if (true === $this->isSecureAllowedForOperation($secure, $operation)) {
                $decision = true;
                break;
            }
        }

        if (null === $decision) {
            $decision = true;
        }

        return $decision;
    }
    /**
     * @param array  $secure
     * @param string $operation
     *
     * @return bool
     */
    protected function isSecureAllowedForOperation(array $secure, $operation)
    {
        $operation = strtolower($operation);

        if (!isset($secure['operations'][$operation])) {
            if (!isset($secure['operations']['all'])) {
                return true;
            }
        }
        if (isset($secure['roles'])) {
            if (!count($secure['roles'])) {
                return true;
            }

            return $this->getAuthorizationChecker()->isGranted(array_keys($secure['roles']));
        }

        return true;
    }
}
