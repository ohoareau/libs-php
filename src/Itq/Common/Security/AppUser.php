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

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AppUser implements UserInterface
{
    /**
     * @var string
     */
    protected $username;
    /**
     * @var string[]
     */
    protected $roles;
    /**
     * @param string $username
     */
    public function __construct($username)
    {
        $this->username = $username;
        $this->roles    = ['ROLE_USER'];
    }
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * @return string
     */
    public function getId()
    {
        return $this->getUsername();
    }
    /**
     * @return string[]
     */
    public function getRoles()
    {
        return $this->roles;
    }
    /**
     * @param array $roles
     *
     * @return $this
     */
    public function addRoles(array $roles)
    {
        $this->roles = array_unique(array_merge($this->roles, array_values($roles)));

        return $this;
    }
    /**
     * @return null
     */
    public function getPassword()
    {
        return null;
    }
    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }
    /**
     *
     */
    public function eraseCredentials()
    {
    }
}
