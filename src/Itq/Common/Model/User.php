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
use JMS\Serializer\Annotation as Jms;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Jms\ExclusionPolicy("all")
 * @Jms\AccessorOrder("alphabetical")
 */
class User implements UserInterface
{
    /**
     * @var bool
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("boolean")
     */
    protected $expired;
    /**
     * @var bool
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("boolean")
     */
    protected $locked;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $firstName;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $lastName;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $email;
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("array<string>")
     * @Jms\Accessor(getter="getFlattenRoles")
     */
    protected $roles;
    /**
     * @var DateTime
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("DateTime<'c'>")
     */
    protected $createDate;
    /**
     * @var DateTime
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("DateTime<'c'>")
     */
    protected $updateDate;
    /**
     * @var bool
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("boolean")
     */
    protected $enabled;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $password;
    /**
     * @var DateTime
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("DateTime<'c'>")
     */
    protected $disableDate;
    /**
     * @var DateTime
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("DateTime<'c'>")
     */
    protected $expireDate;
    /**
     * @var DateTime
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("DateTime<'c'>")
     */
    protected $lockDate;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $token;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $id;
    /**
     * @var array
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("array")
     */
    protected $attributes;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $salt;
    /**
     * @var bool
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("boolean")
     */
    protected $admin;
    /**
     * @var bool
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("boolean")
     */
    protected $allowedToSwitch;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $username;
    /**
     * @var string
     *
     * @Jms\Expose
     * @Jms\Groups({"detailed"})
     * @Jms\Type("string")
     */
    protected $name;
    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $that = $this;

        $config = [
            'defaults' => [
                'expired' => false, 'locked' => false, 'enabled' => true, 'firstName' => null, 'lastName' => null,
                'email' => null, 'salt' => null, 'roles' => [], 'createDate' => null, 'updateDate' => null,
                'disableDate' => null, 'expireDate' => null, 'lockDate' => null, 'password' => null, 'token' => null,
                'id' => null, 'admin' => false, 'allowedToSwitch' => false,
            ],
            'defaultClosures' => [
                'name' => function () use ($that) {
                    return sprintf('%s %s', ucfirst($that->firstName), ucfirst($that->lastName));
                },
                'username' => function () use ($that) {
                    return $that->id;
                },
                'admin' => function () use ($that) {
                    return in_array('ROLE_ADMIN', $that->roles);
                },
                'allowedToSwitch' => function () use ($that) {
                    return true === $that->admin || in_array('ROLE_ALLOWED_TO_SWITCH', $that->roles);
                },
            ],
            'types' => [
                'expired' => 'bool', 'enabled' => 'bool', 'locked' => 'bool',
                'admin' => 'bool', 'allowedToSwitch' => 'bool',
                'firstName' => 'string', 'lastName' => 'string', 'email' => 'string', 'salt' => 'string',
                'password' => 'string', 'token' => 'string', 'id' => 'string',
                'roles' => 'roles',
            ],
        ];

        $data = array_filter(
            $data,
            function ($v) {
                return null !== $v;
            }
        );

        $attributes = [];

        foreach (($data + $config['defaults']) as $key => $value) {
            if (isset($config['types'][$key])) {
                $value = $this->convertValue($value, $config['types'][$key]);
            }
            if (!property_exists($this, $key)) {
                $attributes[$key] = $value;

                continue;
            }
            $this->$key = $value;
        }

        foreach ($config['defaultClosures'] as $key => $closure) {
            if (property_exists($this, $key)) {
                if (!isset($this->$key)) {
                    $this->$key = $closure();
                }
                continue;
            }
            if (!isset($attributes[$key])) {
                $attributes[$key] = $closure();
            }
        }

        $this->attributes = $attributes;
    }
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return !$this->expired;
    }
    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }
    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
    /**
     * @return string[]
     */
    public function getRoles()
    {
        return $this->roles;
    }
    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * @return string|null
     */
    public function getSalt()
    {
        return $this->salt;
    }
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     *
     */
    public function eraseCredentials()
    {
    }
    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : $default;
    }
    /**
     * @return array
     */
    public function getFlattenRoles()
    {
        return array_map(
            function ($v) {
                return strtolower(str_replace(['__', '_'], ['-', '.'], preg_replace('/^ROLE_/', '', $v)));
            },
            $this->getRoles()
        );
    }
    /**
     * @return bool
     */
    public function isAdmin()
    {
        return true === $this->admin;
    }
    /**
     * @return bool
     */
    public function isAllowedToswitch()
    {
        return true === $this->allowedToSwitch;
    }
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->expired;
    }
    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }
    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    /**
     * @return DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }
    /**
     * @return DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
    /**
     * @return DateTime
     */
    public function getDisableDate()
    {
        return $this->disableDate;
    }
    /**
     * @return DateTime
     */
    public function getExpireDate()
    {
        return $this->expireDate;
    }
    /**
     * @return DateTime
     */
    public function getLockDate()
    {
        return $this->lockDate;
    }
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param mixed  $value
     * @param string $type
     *
     * @return mixed
     */
    protected function convertValue($value, $type)
    {
        switch ($type) {
            case 'bool':
                return (bool) $value;
            case 'string':
                return (string) $value;
            case 'roles':
                $value['user'] = true;
                $roles = array_map(
                    function ($v) {
                        return 'ROLE_'.strtoupper(str_replace(['.', '-'], ['_', '__'], $v));
                    },
                    array_keys(array_filter($value))
                );
                sort($roles);

                return $roles;
            default:
                return $value;
        }
    }
}
