<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\TenantAwareInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Tenant Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TenantService
{
    use Traits\ServiceTrait;
    use Traits\TokenStorageAwareTrait;
    /**
     * @param string $sep
     *
     * @return string
     */
    public function getFullType($sep = '.')
    {
        unset($sep);

        return 'tenant';
    }
    /**
     * @param TokenStorageInterface $tokenStorage
     * @param string                $defaultTenant
     */
    public function __construct(TokenStorageInterface $tokenStorage, $defaultTenant)
    {
        $this->setTokenStorage($tokenStorage);
        $this->setDefault($defaultTenant);
    }
    /**
     * @return string
     */
    public function getCurrent()
    {
        $token  = $this->getTokenStorage()->getToken();
        $tenant = null;

        if (null !== $token && $token instanceof TenantAwareInterface) {
            $tenant = $token->getTenant();
        }
        if (null === $tenant) {
            $tenant = $this->getDefault();
        }

        return $tenant;
    }
    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getDefault()
    {
        return $this->getParameter('defaultTenant');
    }
    /**
     * @param string $tenant
     *
     * @return $this
     */
    public function setDefault($tenant)
    {
        return $this->setParameter('defaultTenant', $tenant);
    }
}
