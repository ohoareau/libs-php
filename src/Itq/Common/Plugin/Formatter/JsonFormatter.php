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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class JsonFormatter extends Base\AbstractSerializerFormatter
{
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("application/json")
     * @Annotation\Formatter("text/json")
     */
    public function format($data, array $options = [])
    {
        return $this->handleFormat($data, 'json', $options);
    }
}
