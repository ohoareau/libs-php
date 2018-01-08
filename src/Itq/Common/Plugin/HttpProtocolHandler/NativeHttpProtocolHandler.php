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

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NativeHttpProtocolHandler extends Base\AbstractHttpProtocolHandler
{
    use Traits\HttpHeadersParserTrait;
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
        $options += [
            'timeout' => 10,
            'method'  => 'GET',
            'header'  => count($headers) ? (join("\r\n", $headers)."\r\n") : null,
            'content' => null !== $data ? (is_array($data) ? json_encode($data) : (string) $data) : null,
        ];

        $context  = stream_context_create(
            [
                'http' => [
                    'method' => $options['method'],
                    'timeout' => $options['timeout'],
                ]
                + (isset($options['header']) ? ['header' => $options['header']] : [])
                + (isset($options['content']) ? ['content' => $options['content']] : [])
                ,
            ]
        );

        $result = file_get_contents(sprintf('%s://%s%s', $protocol, $domain, $uri), false, $context);

        return $this->parseRawHttpHeaders($http_response_header) + ['content' => $result];
    }
}
