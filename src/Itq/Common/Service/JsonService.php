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

/**
 * Json Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class JsonService
{
    use Traits\ServiceTrait;
    /**
     * @param mixed $value
     * @param array $options
     *
     * @return string
     */
    public function serialize($value, array $options = [])
    {
        unset($options);

        return json_encode($value);
    }
    /**
     * @param string $string
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function unserialize($string, array $options = [])
    {
        if (!is_string($string)) {
            throw $this->createMalformedException('Only string are JSON unserializable');
        }

        $options += ['array' => true];

        return json_decode($string, $options['array']);
    }
}
