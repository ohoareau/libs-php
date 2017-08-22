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
use Itq\Common\Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class UserProviderService implements UserProviderInterface
{
    use Traits\ServiceTrait;
    /**
     * @param string $userClass
     */
    public function __construct($userClass)
    {
        $this->setUserClass($userClass);
    }
    /**
     * @param string $userClass
     *
     * @return $this
     */
    public function setUserClass($userClass)
    {
        return $this->setParameter('userClass', $userClass);
    }
    /**
     * @return string
     */
    public function getUserClass()
    {
        return $this->getParameter('userClass');
    }
    /**
     * @param mixed  $accountProvider
     * @param string $type
     * @param string $method
     * @param string $format
     * @param bool   $alreadyAuthentified
     * @param null   $usernameKeys
     *
     * @return $this
     */
    public function setAccountProvider($accountProvider, $type = 'default', $method = 'get', $format = 'plain', $alreadyAuthentified = false, $usernameKeys = null)
    {
        return $this->setArrayParameterKey(
            'accountProviders',
            $type,
            [
                'method'       => $method,
                'format'       => $format,
                'provider'     => $accountProvider,
                'authentified' => $alreadyAuthentified,
                'usernameKeys' => is_array($usernameKeys) ? $usernameKeys : ['id'],
            ]
        );
    }
    /**
     * @param string $username
     *
     * @return UserInterface
     *
     * @throws \Exception
     */
    public function loadUserByUsername($username)
    {
        $account = null;

        try {
            $account = $this->getAccount($username);
        } catch (\Exception $e) {
            if (404 === $e->getCode()) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            } elseif (412 === $e->getCode()) {
                throw new AuthenticationException(sprintf("Unable to retrieve username '%s': %s", $username, $e->getMessage()), 412);
            }
            throw $e;
        }

        $class = $this->getUserClass();

        return new $class($account);
    }
    /**
     * @param string $username
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getAccount($username)
    {
        $realUsername = $username;
        $type         = 'default';

        if (false !== strpos($username, '/')) {
            list($type, $realUsername) = explode('/', $username, 2);
        }
        if (!$this->hasArrayParameterKey('accountProviders', $type)) {
            throw new Exception\UnsupportedAccountTypeException($type);
        }

        $accountProviderDescription = $this->getArrayParameterKey('accountProviders', $type);
        $accountProvider            = $accountProviderDescription['provider'];
        $method                     = $accountProviderDescription['method'];
        $format                     = $accountProviderDescription['format'];
        $alreadyAuthentified        = true === $accountProviderDescription['authentified'];
        $usernameKeys               = (array) $accountProviderDescription['usernameKeys'];

        if (!method_exists($accountProvider, $method)) {
            throw $this->createNotFoundException(
                "Unable to retrieve account from account provider '%s' (method: %s)",
                get_class($accountProvider),
                $method
            );
        }

        $a = $accountProvider->{$method}($this->unformat($realUsername, $format));

        if (is_object($a)) {
            $a = get_object_vars($a);
        }

        foreach ($usernameKeys as $usernameKey) {
            if (isset($a[$usernameKey])) {
                $a['username'] = $a[$usernameKey];
                break;
            }
        }

        return ['*alreadyAuthentified*' => (bool) $alreadyAuthentified] + $a;
    }
    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws \Exception
     */
    public function refreshUser(UserInterface $user)
    {
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
    /**
     * @param string $value
     * @param string $format
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function unformat($value, $format)
    {
        switch ($format) {
            case 'base64':
                $decoded = base64_decode($value);
                if (function_exists('mb_detect_encoding') && !mb_detect_encoding($decoded, ['UTF-8', 'ASCII'], true)) {
                    throw $this->createMalformedException("Value is not valid base64 encoded UTF-8/ASCII");
                }

                return $decoded;
            case 'plain':
                return $value;
            default:
                return $value;
        }
    }
}
