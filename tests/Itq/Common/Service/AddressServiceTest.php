<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Model\Internal\Address;
use Itq\Common\Service\AddressService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/address
 */
class AddressServiceTest extends AbstractServiceTestCase
{
    /**
     * @return AddressService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mockedHttpService(),
            'http://localhost/test',
        ];
    }
    /**
     * @group integ
     *
     *
     * @dataProvider getLocationGeocodingData
     *
     * @param string $location
     * @param array  $expectedData
     */
    public function testLocationGeocoding($location, array $expectedData)
    {
        $this->markTestSkipped("As the Data Gouv API is subject to non-BC updates, disable for now");

        $this->s()->setApiAddress('http://api-adresse.data.gouv.fr');

        $address = $this->s()->getByLocation($location);

        $this->assertTrue($address instanceof Address);

        $actualData = (array) $address;

        $this->assertEquals($expectedData, $actualData);
    }
    /**
     * @return array
     */
    public function getLocationGeocodingData()
    {
        return [
            ['Paris', ['type' => 'city', 'longitude' => 2.3469, 'latitude' => 48.8589, 'location' => 'Paris', 'street' => 'Paris', 'complement' => null, 'city' => 'Paris', 'zipCode' => '75000']],
            ['10 Avenue Simone Veil, 69150 Décines-Charpieu', ['type' => 'housenumber', 'longitude' => 4.9843469999999996, 'latitude' => 45.760178000000003, 'location' => '10 Avenue Simone Veil, 69150 Décines-Charpieu', 'street' => '10 Avenue Simone Veil', 'complement' => null, 'city' => 'Décines-Charpieu', 'zipCode' => '69150']],
        ];
    }
}
