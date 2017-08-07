<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Formatter;

use Itq\Common\Plugin\Base\AbstractPlugin;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

use Symfony\Component\Yaml\Yaml;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class YamlFormatter extends AbstractPlugin
{
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("application/x-yaml")
     * @Annotation\Formatter("text/yaml")
     */
    public function format($data, array $options = [])
    {
        unset($options);

        return Yaml::dump($data);
    }
}
