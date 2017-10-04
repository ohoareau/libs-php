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

use Itq\Common\Service\VaultService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/vault
 */
class VaultServiceTest extends AbstractServiceTestCase
{
    /**
     * @return VaultService
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
        return [$this->mockedStorageService()];
    }
    /**
     * @group unit
     */
    public function testSavePassword()
    {
        $key = 'key';
        $value = 'value';

        $this->mockedStorageService()->expects($this->once())->method('save')->with(sprintf('/registry/passwords/%s', md5($key)), $value);

        $this->s()->savePassword($key, $value);
    }
    /**
     * @group unit
     */
    public function testRetrievePassword()
    {
        $key = 'key';
        $value = 'value';

        $this
            ->mockedStorageService()->expects($this->once())->method('read')
            ->with(sprintf('/registry/passwords/%s', md5($key)))
            ->will($this->returnValue($value));

        $this->assertEquals($value, $this->s()->retrievePassword($key));
    }
}
