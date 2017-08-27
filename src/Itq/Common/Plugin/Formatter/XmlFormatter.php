<?php

/*
 * This file is part of the WS package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Formatter;

use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class XmlFormatter extends Base\AbstractSerializerFormatter
{
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("text/xml")
     */
    public function format($data, array $options = [])
    {
        return $this->handleFormat($data, 'xml', $options);
    }
}
