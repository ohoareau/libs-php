<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait HttpHeadersParserTrait
{
    /**
     * @param array $rawHeaders
     *
     * @return array
     */
    protected function parseRawHttpHeaders(array $rawHeaders)
    {
        $statusCode    = 200;
        $statusMessage = null;
        $headers       = [];

        foreach ($rawHeaders as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $key   = strtolower(trim($t[0]));
                $value = trim($t[1]);
                if (isset($headers[$key])) {
                    if (!is_array($headers[$key])) {
                        $headers[$key] = [$headers[$key]];
                    }
                    $headers[$key][] = $value;
                } else {
                    $headers[$key] = $value;
                }
            } else {
                $matches = null;
                if (0 < preg_match("#HTTP/[0-9\.]+\s+([0-9]+)(\s+(.+))?#", $v, $matches)) {
                    $statusCode = intval($matches[1]);
                    if (isset($matches[3])) {
                        $statusMessage = $matches[3];
                    }
                    continue;
                }
            }
        }

        return ['statusCode' => $statusCode, 'statusMessage' => $statusMessage, 'headers' => $headers];
    }
}
