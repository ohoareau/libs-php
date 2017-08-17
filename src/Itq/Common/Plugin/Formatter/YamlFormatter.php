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

use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class YamlFormatter extends Base\AbstractFormatter
{
    use Traits\ServiceAware\YamlServiceAwareTrait;
    /**
     * @param Service\YamlService $yamlService
     */
    public function __construct(Service\YamlService $yamlService)
    {
        $this->setYamlService($yamlService);
    }
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("application/x-yaml")
     * @Annotation\Formatter("text/yaml")
     */
    public function format($data, /** @noinspection PhpUnusedParameterInspection */ array $options = [])
    {
        return $this->getYamlService()->serialize($data);
    }
}
