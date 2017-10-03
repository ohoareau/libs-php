<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Traits;

use Itq\Common\Traits\RequestHelperTrait;
use Symfony\Component\HttpFoundation\Request;
use Itq\Common\Tests\Base\AbstractBasicTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group traits
 * @group traits/request-helper
 */
class RequestHelperTraitTest extends AbstractBasicTestCase
{
    use RequestHelperTrait;
    /**
     * @param mixed   $expected
     * @param array   $params
     * @param Request $request
     *
     * @group unit
     *
     * @dataProvider getParseParamsFromRequestData
     */
    public function testParseParamsFromRequest($expected, $params, $request)
    {
        $this->assertEquals($expected, $this->parseParamsFromRequest($params, $request));
    }
    /**
     * @return array
     */
    public function getParseParamsFromRequestData()
    {
        return [
            [[], [], new Request()],
            [['a' => 'b'], ['a' => 'b'], new Request()],
            [['a' => 'c'], ['a' => '%b%'], new Request([], [], ['b' => 'c'])],
            [['a' => null], ['a' => '%b%'], new Request([], [], ['c' => 'd'])],
        ];
    }
}
