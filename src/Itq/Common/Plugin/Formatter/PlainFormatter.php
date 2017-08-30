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
class PlainFormatter extends Base\AbstractFormatter
{
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("text/plain")
     */
    public function formatPlain($data, array $options = [])
    {
        return (string) $data;
    }
}
