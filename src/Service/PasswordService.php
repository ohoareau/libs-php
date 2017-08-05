<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * Password Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PasswordService
{
    use Traits\ServiceTrait;
    /**
     * Test if specified raw value is matching the specified expected encoded password.
     *
     * If salt is provided in options, take it into account.
     *
     * @param string $raw
     * @param string $encoded
     * @param array  $options
     *
     * @return bool true if matching, false otherwise
     */
    public function test($raw, $encoded, array $options = [])
    {
        return $encoded === $this->encrypt($raw, $options);
    }
    /** @noinspection PhpInconsistentReturnPointsInspection */
    /**
     * Encrypt the specified raw password.
     *
     * If salt is provided in options, take it into account.
     * If algorithm is provided in options, try to use the specified algorithm.
     *
     * @param string $raw
     * @param array  $options
     *
     * @return string
     *
     * @throws \Exception
     */
    public function encrypt($raw, array $options = [])
    {
        if (!isset($options['algorithm'])) {
            $options['algorithm'] = 'default';
        }

        switch ($options['algorithm']) {
            case 'default':
                return $this->encryptWithDefault($raw, $options);
        }

        throw $this->createUnexpectedException(
            "Unsupported algorithm '%s'",
            $options['algorithm']
        );
    }
    /** @noinspection PhpInconsistentReturnPointsInspection */
    /**
     * Generate a password based on the specified data and options.
     *
     * @param array $data
     * @param array $options
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generate(array $data = [], array $options = [])
    {
        if ($this->isSpecialData($data, $options)) {
            $options['generator'] = 'special';
        }

        if (!isset($options['generator'])) {
            $options['generator'] = 'default';
        }

        switch ($options['generator']) {
            case 'default':
                return $this->generateWithDefault($data, $options);
            case 'special':
                return $this->generateWithSpecial($data, $options);
        }

        throw $this->createUnexpectedException(
            "Unsupported generator '%s'",
            $options['generator']
        );
    }
    /**
     * Test if specified data are special.
     *
     * @param mixed $data
     * @param array $options
     *
     * @return bool
     */
    protected function isSpecialData(array $data, array $options = [])
    {
        unset($options);

        return isset($data['email']) && preg_match('/^.+\+test.*@.*$/', $data['email']);
    }
    /**
     * Encrypts the specified raw with default algorithm.
     *
     * If salt is provided in options, take it into account.
     *
     * @param mixed $raw
     * @param array $options
     *
     * @return string
     */
    protected function encryptWithDefault($raw, array $options = [])
    {
        return sha1(md5(sha1(md5(
            $raw.(isset($options['salt']) ? $options['salt'] : null)
        ))));
    }
    /**
     * Generate a new password with the default generator.
     *
     * @param array $data
     * @param array $options
     *
     * @return string
     */
    protected function generateWithDefault(array $data = [], array $options = [])
    {
        unset($data);

        $types = [
            'lowercasedLetters' => [3, ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']],
            'uppercasedLetters' => [2, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']],
            'digits'            => [1, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']],
            'punctuations'      => [0, ['@', '%', ';']],
        ];

        $chars = [];

        foreach ($types as $data) {
            list($nb, $items) = $data;
            for ($i = 0; $i < $nb; $i++) {
                $chars[] = $items[rand(0, count($items) - 1)];
            }
        }

        unset($options);

        return implode($chars);
    }
    /**
     * Generate a new password with the special generator.
     *
     * @param array $data
     * @param array $options
     *
     * @return string
     */
    protected function generateWithSpecial(array $data = [], array $options = [])
    {
        unset($options);

        return substr(md5(isset($data['email']) ? $data['email'] : []), 0, 8);
    }
}
