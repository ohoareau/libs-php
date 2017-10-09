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

use Itq\Common\Service\DynamicUrlService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/dynamic-url
 */
class DynamicUrlServiceTest extends AbstractServiceTestCase
{
    /**
     * @return DynamicUrlService
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
        return [$this->mockedGeneratorService()];
    }
    /**
     * @param string $type
     * @param array  $def
     * @param array  $return
     *
     * @group unit
     * @dataProvider getComputeData
     */
    public function testCompute($type, $def, $return)
    {
        $def += ['type' => $type];
        $this->mockedGeneratorService()->expects($this->any())->method('generate')->with('dynamicurl', $return);
        $this->s()->compute((object) ['foo' => (object) ['bar' => 1]], $def, array());
    }
    /**
     * @return array
     */
    public function getComputeData()
    {
        return [
            '1 - Test simple param' => ['%app_app_website_premium_url%/g/{bar}', ['vars' => ['bar' => 'myStringUrl' ]], ['dynamicPattern' =>'%app_app_website_premium_url%/g/myStringUrl', 'bar' => 'myStringUrl'  ] ],
            '2 - Test no param' => ['%app_app_website_premium_url%/g/{foo}', [] ,['dynamicPattern' => '%app_app_website_premium_url%/g/{foo}'] ],
            '3 - Test undefined pointer param' => ['%app_app_website_premium_url%/g/{bar}', ['vars' => ['bar' => '@myStringUrl.bar' ]], ['dynamicPattern' => '%app_app_website_premium_url%/g/myStringUrl', 'bar' => 'myStringUrl' ] ],
            '4 - Test pointer param' => ['%app_app_website_premium_url%/g/{foo}', ['vars' => ['foo' => '@foo.bar' ]], ['dynamicPattern' => '%app_app_website_premium_url%/g/1', 'foo' => 1] ],
        ];
    }
}
