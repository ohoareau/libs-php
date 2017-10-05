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

use Itq\Common\Service\TenantService;
use Itq\Common\Aware\TenantAwareInterface;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/tenant
 */
class TenantServiceTest extends AbstractServiceTestCase
{
    /**
     * @return TenantService
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
            $this->mockedTokenStorage(),
            'testtenant',
        ];
    }
    /**
     * @group unit
     */
    public function testGetFullType()
    {
        $this->assertEquals('tenant', $this->s()->getFullType());
    }
    /**
     * @param string|null $tenant
     * @param string      $defaultToken
     * @param string      $expected
     *
     * @group unit
     * @dataProvider provideGetCurrentData
     */
    public function testGetCurrent($tenant, $defaultToken, $expected)
    {
        if (null !== $tenant) {
            $this->mocked('tenantAware', TenantAwareInterface::class);
            $this->mocked('tenantAware')->expects($this->once())->method('getTenant')->will($this->returnValue($tenant));

            $tokenStorageReturn = $this->mocked('tenantAware');
        } else {
            $tokenStorageReturn = null;
        }

        $this->mockedTokenStorage()->expects($this->once())->method('getToken')->will($this->returnValue($tokenStorageReturn));

        $this->s()->setDefault($defaultToken);
        $this->assertEquals($expected, $this->s()->getCurrent());
    }
    /**
     * @return array
     */
    public function provideGetCurrentData()
    {
        return [
            ['token1', 'default_tenant', 'token1'],
            [ null, 'default_tenant', 'default_tenant'],
        ];
    }
}
