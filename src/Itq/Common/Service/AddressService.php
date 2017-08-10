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

use Itq\Common\Model;
use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AddressService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\HttpServiceAwareTrait;
    /**
     * @param Service\HttpService $httpService
     * @param string              $apiAddress
     */
    public function __construct(Service\HttpService $httpService, $apiAddress)
    {
        $this->setApiAddress($apiAddress);
    }
    /**
     * @param string $apiAddress
     *
     * @return $this
     */
    public function setApiAddress($apiAddress)
    {
        return $this->setParameter('apiAddress', $apiAddress);
    }
    /**
     * @return string
     */
    public function getApiAddress()
    {
        return $this->getParameter('apiAddress');
    }
    /**
     * @param string $location
     * @param array  $fields
     * @param array  $options
     *
     * @return Model\Internal\Address
     *
     * @throws \Exception
     */
    public function getByLocation($location, array $fields = [], array $options = [])
    {
        unset($options);

        $response = $this
            ->getHttpService()
            ->jsonRequest(sprintf('%s/search/?q=%s&limit=1', $this->getApiAddress(), urlencode($location)))
        ;

        if (!$response['content']) {
            throw $this->createNotFoundException('Unable to retrieve address from specified location');
        }

        if (!isset($response['content']['features'][0])) {
            throw $this->createNotFoundException('Unable to retrieve address from specified location');
        }

        $foundFields = [
            'type'      => $response['content']['features'][0]['properties']['type'],
            'location'  => $response['content']['features'][0]['properties']['label'],
            'city'      => $response['content']['features'][0]['properties']['city'],
            'street'    => $response['content']['features'][0]['properties']['name'],
            'longitude' => $response['content']['features'][0]['geometry']['coordinates'][0],
            'latitude'  => $response['content']['features'][0]['geometry']['coordinates'][1],
            'zipCode'   => $response['content']['features'][0]['properties']['postcode'],
        ];

        if (!count($fields)) {
            $fields = array_keys($foundFields);
        }

        return new Model\Internal\Address(array_intersect_key($foundFields, array_fill_keys($fields, true)));
    }
}
