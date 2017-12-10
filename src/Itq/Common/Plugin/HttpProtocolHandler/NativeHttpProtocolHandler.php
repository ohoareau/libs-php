<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\HttpProtocolHandler;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NativeHttpProtocolHandler extends Base\AbstractHttpProtocolHandler
{
    /**
     * @param string $protocol
     * @param string $domain
     * @param string $uri
     * @param string $data
     * @param array  $headers
     * @param array  $options
     *
     * @return array
     *
     * @throws \Exception
     */
    public function request($protocol, $domain, $uri, $data, array $headers = [], array $options = [])
    {
        $options += ['timeout' => 10, 'method' => 'GET'];
        $context  = stream_context_create(['http' => ['method' => $options['method'], 'timeout' => $options['timeout']]]);
        $result   = file_get_contents(sprintf('%s://%s%s', $protocol, $domain, $uri), false, $context);

        return ['statusCode' => 200, 'statusMessage' => 'OK', 'content' => $result];
    }
}
