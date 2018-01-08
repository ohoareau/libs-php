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

use Itq\Common\Traits\HttpHeadersParserTrait;
use Itq\Common\Tests\Base\AbstractBasicTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group traits
 * @group traits/http-headers-parser
 */
class HttpHeadersParserTraitTest extends AbstractBasicTestCase
{
    use HttpHeadersParserTrait;
    /**
     * @param mixed $expected
     * @param array $rawHeaders
     *
     * @group unit
     *
     * @dataProvider getParseRawHttpHeadersData
     */
    public function testParseRawHttpHeaders($expected, $rawHeaders)
    {
        $this->assertEquals($expected, $this->parseRawHttpHeaders($rawHeaders));
    }
    /**
     * @return array
     */
    public function getParseRawHttpHeadersData()
    {
        return [
            [['statusCode' => 200, 'statusMessage' => null, 'headers' => []], []],
            [['statusCode' => 200, 'statusMessage' => null, 'headers' => ['a' => 'b']], ['a: b']],
            [['statusCode' => 200, 'statusMessage' => null, 'headers' => ['a' => 'b']], ['A: b']],
            [['statusCode' => 200, 'statusMessage' => null, 'headers' => ['a' => 'b', 'c' => 'd', 'e' => 'F']], ['A: b', 'c: d', 'E: F']],
            [['statusCode' => 200, 'statusMessage' => 'OK', 'headers' => ['e' => 'F']], ['E: F', 'HTTP/1.0 200 OK']],
            [['statusCode' => 403, 'statusMessage' => 'Forbidden', 'headers' => ['e' => 'F']], ['E: F', 'HTTP/1.0 403 Forbidden']],
            [['statusCode' => 500, 'statusMessage' => 'Server Error', 'headers' => []], ['HTTP/1.0 500 Server Error']],
            [['statusCode' => 302, 'statusMessage' => 'Moved Temporarily', 'headers' => ['zz' => 'tt']], ['HTTP/1.0 302 Moved Temporarily', 'zz: tt']],
            [['statusCode' => 401, 'statusMessage' => 'Authorization Required', 'headers' => ['xxx' => ['a', 'B']]], ['HTTP/1.0 401 Authorization Required', 'xxx: a', 'xxx: B']],
        ];
    }
}
