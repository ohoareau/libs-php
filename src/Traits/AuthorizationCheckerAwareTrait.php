<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * AuthorizationCheckerAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait AuthorizationCheckerAwareTrait
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
     * @param AuthorizationCheckerInterface $authorizationChecker
     *
     * @return $this
     */
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        return $this->setService('authorizationChecker', $authorizationChecker);
    }
    /**
     * @return AuthorizationCheckerInterface
     */
    public function getAuthorizationChecker()
    {
        return $this->getService('authorizationChecker');
    }
}
