<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Model;

use DateTime;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface UserInterface extends AdvancedUserInterface
{
    /**
     * @return string
     */
    public function getEmail();
    /**
     * @return bool
     */
    public function isAccountNonExpired();
    /**
     * @return bool
     */
    public function isAccountNonLocked();
    /**
     * @return bool
     */
    public function isCredentialsNonExpired();
    /**
     * @return bool
     */
    public function isEnabled();
    /**
     * @return string[]
     */
    public function getRoles();
    /**
     * @return string
     */
    public function getPassword();
    /**
     * @return string|null
     */
    public function getSalt();
    /**
     * @return string
     */
    public function getUsername();
    /**
     *
     */
    public function eraseCredentials();
    /**
     * @return array
     */
    public function getAttributes();
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute($key, $default = null);
    /**
     * @return array
     */
    public function getFlattenRoles();
    /**
     * @return bool
     */
    public function isAdmin();
    /**
     * @return bool
     */
    public function isAllowedToswitch();
    /**
     * @return string
     */
    public function getId();
    /**
     * @return bool
     */
    public function isExpired();
    /**
     * @return bool
     */
    public function isLocked();
    /**
     * @return string
     */
    public function getFirstName();
    /**
     * @return string
     */
    public function getLastName();
    /**
     * @return DateTime
     */
    public function getCreateDate();
    /**
     * @return DateTime
     */
    public function getUpdateDate();
    /**
     * @return DateTime
     */
    public function getDisableDate();
    /**
     * @return DateTime
     */
    public function getExpireDate();
    /**
     * @return DateTime
     */
    public function getLockDate();
    /**
     * @return string
     */
    public function getToken();
    /**
     * @return string
     */
    public function getName();
}
