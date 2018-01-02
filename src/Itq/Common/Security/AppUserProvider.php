<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Security;

use Itq\Common\Traits;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AppUserProvider implements UserProviderInterface
{
    use Traits\ServiceTrait;
    use Traits\ParameterAware\UserClassParameterAwareTrait;
    /**
     * @param string $userClass
     */
    public function __construct($userClass)
    {
        $this->setUserClass($userClass);
    }
    /**
     * @param string $username
     *
     * @return AppUser
     */
    public function loadUserByUsername($username)
    {
        $class = $this->getUserClass();

        return new $class($username);
    }
    /**
     * @param UserInterface $user
     *
     * @return AppUser
     */
    public function refreshUser(UserInterface $user)
    {
        $class = $this->getUserClass();

        if (!$user instanceof $class) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }
    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === $this->getUserClass();
    }
}
