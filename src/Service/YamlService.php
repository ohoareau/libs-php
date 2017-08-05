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

use Symfony\Component\Yaml\Yaml;

/**
 * Yaml Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class YamlService
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
        $options += ['inlineLevel' => 3, 'indentSize' => 4];

        return Yaml::dump($value, $options['inlineLevel'], $options['indentSize']);
    }
    /**
     * @param string $string
     * @param array  $options
     *
     * @return array
     *
     * @throws \Exception
     */
    public function unserialize($string, array $options = [])
    {
        if (!is_string($string)) {
            throw $this->createMalformedException('Only string are YAML unserializable');
        }

        unset($options);

        return Yaml::parse($string);
    }
}
