<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface HttpProtocolHandlerInterface
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
     */
    public function request($protocol, $domain, $uri, $data, array $headers = [], array $options = []);
}
