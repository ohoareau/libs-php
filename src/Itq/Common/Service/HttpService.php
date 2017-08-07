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
use Itq\Common\Plugin\HttpProtocolHandlerInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class HttpService
{
    use Traits\ServiceTrait;
    /**
     * @param string|array                 $protocol
     * @param HttpProtocolHandlerInterface $protocolHandler
     * @param array                        $methods
     *
     * @return $this
     */
    public function registerProtocolHandler(
        $protocol,
        HttpProtocolHandlerInterface $protocolHandler,
        array $methods = ['*']
    ) {
        if (!is_array($protocol)) {
            $protocol = [$protocol];
        }

        foreach ($protocol as $p) {
            foreach ($methods as $method) {
                $this->setArrayParameterKey(
                    'protocolHandlers',
                    sprintf('%s:%s', strtolower($p), strtolower($method)),
                    $protocolHandler
                );
            }
        }

        return $this;
    }
    /**
     * @param string $url
     * @param string $method
     * @param string $data
     * @param array  $headers
     * @param array  $options
     *
     * @return array
     *
     * @throws \Exception
     */
    public function request($url, $method = 'GET', $data = null, array $headers = [], array $options = [])
    {
        list ($protocol, $domain, $uri) = $this->parseUrl($url);

        return $this
            ->getProtocolHandlerByProtocolAndMethod($protocol, $method)
            ->request($protocol, $domain, $uri, $data, $headers, $options)
        ;
    }
    /**
     * @param string $url
     * @param string $method
     * @param null   $data
     * @param array  $headers
     * @param array  $options
     *
     * @return array
     */
    public function jsonRequest($url, $method = 'GET', $data = null, array $headers = [], array $options = [])
    {
        $response = $this->request($url, $method, $data, $headers, $options);

        $response['rawContent'] = $response['content'];

        if (null !== $response['content']) {
            $response['content'] = @json_decode($response['content'], true);
        }

        return $response;
    }
    /**
     * @param string $protocol
     * @param string $method
     *
     * @return bool
     */
    public function hasProtocolHandlerByProtocolAndMethod($protocol, $method)
    {
        return $this->hasArrayParameterKey('protocolHandlers', sprintf('%s:%s', strtolower($protocol), strtolower($method)));
    }
    /**
     * @param string $protocol
     * @param string $method
     *
     * @return HttpProtocolHandlerInterface
     *
     * @throws \Exception
     */
    public function getProtocolHandlerByProtocolAndMethod($protocol, $method)
    {
        $protocol = strtolower($protocol);
        $method   = strtolower($method);

        if (!$this->hasProtocolHandlerByProtocolAndMethod($protocol, $method)) {
            throw $this->createFailedException(
                sprintf("No Http Protocol handler registered for protocol '%s' and method '%s'", $protocol, $method)
            );
        }

        return $this->getArrayParameterKey('protocolHandlers', sprintf('%s:%s', $protocol, $method));
    }
    /**
     * @param string $url
     *
     * @return array
     *
     * @throws \Exception
     */
    public function parseUrl($url)
    {
        $matches = null;

        if (0 >= preg_match(',^([^\:]+)\://([^/]+)(.*)$,', $url, $matches)) {
            throw $this->createMalformedException("Url must be formatted '[protocol]://[domain][uri]'");
        }

        return [strtolower($matches[1]), $matches[2], $matches[3] ?: '/'];
    }
}
