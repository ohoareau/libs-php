<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Generator;

use Itq\Common\Traits;
use Itq\Common\Service;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PasswordGenerator extends Base\AbstractGenerator
{
    use Traits\ServiceAware\VaultServiceAwareTrait;
    use Traits\ServiceAware\PasswordServiceAwareTrait;
    /**
     * @param Service\PasswordService $passwordService
     * @param Service\VaultService    $vaultService
     */
    public function __construct(Service\PasswordService $passwordService, Service\VaultService $vaultService)
    {
        $this->setPasswordService($passwordService);
        $this->setVaultService($vaultService);
    }
    /**
     * @Annotation\Generator("password")
     *
     * @param array $data
     * @param array $options
     *
     * @return string
     */
    public function generatePassword($data = [], $options = [])
    {
        unset($data);

        $options += [
            'lowerCount'  => 4,
            'upperCount'  => 2,
            'digitCount'  => 3,
            'miscCount'   => 1,
            'customCount' => 0,
            'lowerList'   => ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
            'upperList'   => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
            'digitList'   => ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            'miscList'    => ['@', '%', ';', '!', '_'],
            'customList'  => [],
        ];

        $types = [
            'lower'  => [$options['lowerCount'], $options['lowerList']],
            'upper'  => [$options['upperCount'], $options['upperList']],
            'digit'  => [$options['digitCount'], $options['digitList']],
            'misc'   => [$options['miscCount'], $options['miscList']],
            'custom' => [$options['customCount'], $options['customList']],
        ];

        $chars = [];

        foreach ($types as $data) {
            list($nb, $items) = $data;
            for ($i = 0; $i < $nb; $i++) {
                $chars[] = $items[rand(0, count($items) - 1)];
            }
        }

        shuffle($chars);

        unset($options);

        return implode($chars);
    }
    /**
     * @Annotation\Generator("encrypted_password")
     *
     * @param array $data
     * @param array $options
     *
     * @return string
     */
    public function generateEncryptedPassword($data = [], $options = [])
    {
        $raw       = $this->generatePassword($data, $options);
        $encrypted = $this->getPasswordService()->encrypt($raw, $options);

        if (isset($options['vaultKey'])) {
            $key = $options['vaultKey'];
            foreach ($data as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    continue;
                }
                $key = str_replace('{'.$k.'}', $v, $key);
            }
            $this->getVaultService()->savePassword($key, $raw);
        }

        return $encrypted;
    }
}
