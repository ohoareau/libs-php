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
use Itq\Common\ClientProviderInterface;

/**
 * Decorated Client Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DecoratedClientService implements ClientProviderInterface
{
    use Traits\ServiceTrait;
    /**
     * @param mixed  $clientService
     * @param string $method
     * @param string $format
     *
     * @throws \Exception
     */
    public function __construct($clientService, $method = 'get', $format = 'raw')
    {
        if (!method_exists($clientService, $method)) {
            throw $this->createRequiredException(
                "Missing method %s::%s()",
                get_class($clientService),
                $method
            );
        }

        $this->setParameter('client', $clientService);
        $this->setParameter('method', $method);
        $this->setParameter('format', $format);
    }
    /**
     * Return the client.
     *
     * @param string $id
     * @param array  $fields
     * @param array  $options
     *
     * @return mixed
     */
    public function get($id, $fields = [], $options = [])
    {
        return $this->getParameter('client')->{$this->getParameter('method')}(
            $this->unformat($id), $fields, $options
        );
    }
    /**
     * @param mixed $formattedValue
     *
     * @return string
     */
    protected function unformat($formattedValue)
    {
        switch ($this->getParameter('format')) {
            case 'base64':
                return base64_decode($formattedValue);
            default:
            case 'raw':
                return $formattedValue;
        }
    }
}
