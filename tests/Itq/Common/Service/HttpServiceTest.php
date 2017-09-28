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

use Itq\Common\Service;
use Itq\Common\Plugin\HttpProtocolHandlerInterface;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/http
 */
class HttpServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\HttpService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testRegisterProtocolHandler()
    {
        $mock = $this->mocked('testProtocolHandler', HttpProtocolHandlerInterface::class);

        $this->assertFalse($this->s()->hasProtocolHandlerByProtocolAndMethod('a', 'get'));
        $this->assertFalse($this->s()->hasProtocolHandlerByProtocolAndMethod('A', 'get'));
        $this->assertFalse($this->s()->hasProtocolHandlerByProtocolAndMethod('A', 'GET'));
        $this->assertFalse($this->s()->hasProtocolHandlerByProtocolAndMethod('A', 'Get'));
        $this->s()->registerProtocolHandler('a', $mock, ['get']);

        $this->assertTrue($this->s()->hasProtocolHandlerByProtocolAndMethod('a', 'get'));
        $this->assertTrue($this->s()->hasProtocolHandlerByProtocolAndMethod('A', 'get'));
        $this->assertTrue($this->s()->hasProtocolHandlerByProtocolAndMethod('A', 'GET'));
        $this->assertTrue($this->s()->hasProtocolHandlerByProtocolAndMethod('A', 'Get'));
        $this->assertEquals($mock, $this->s()->getProtocolHandlerByProtocolAndMethod('a', 'get'));
        $this->assertEquals($mock, $this->s()->getProtocolHandlerByProtocolAndMethod('a', 'GET'));

        $this->assertFalse($this->s()->hasProtocolHandlerByProtocolAndMethod('C', 'POST'));
        $this->assertFalse($this->s()->hasProtocolHandlerByProtocolAndMethod('d', 'post'));
        $this->s()->registerProtocolHandler(['c', 'D'], $mock, ['poSt']);
        $this->assertTrue($this->s()->hasProtocolHandlerByProtocolAndMethod('C', 'POST'));
        $this->assertTrue($this->s()->hasProtocolHandlerByProtocolAndMethod('d', 'post'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("No Http Protocol handler registered for protocol 'unknown' and method 'unknown'");
        $this->expectExceptionCode(500);

        $this->s()->getProtocolHandlerByProtocolAndMethod('unknown', 'unknown');
    }
    /**
     * @param array|\Exception $expected
     * @param string|mixed     $url
     *
     * @group unit
     * @dataProvider getParseUrlData
     */
    public function testParseUrl($expected, $url)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
            $this->expectExceptionCode($expected->getCode());
        }

        $result = $this->s()->parseUrl($url);

        if (!($expected instanceof \Exception)) {
            $this->assertEquals($expected, $result);
        }
    }
    /**
     * @return array
     */
    public function getParseUrlData()
    {
        return [
            [['a', 'b', '/c'], 'a://b/c'],
            [['a', 'b', '/'], 'a://b/'],
            [['a', 'b', '/'], 'a://b'],
            [['a', 'b', '/?p='], 'a://b?p='],
            [['a', 'b', '/?p='], 'a://b/?p='],
            [new \RuntimeException("Url must be formatted '[protocol]://[domain][uri]'", 412), 'abc'],
        ];
    }
    /**
     * @param array|\Exception $expectedResponse
     * @param array|\Exception $mockedProtocolHandlerResponse
     * @param string           $url
     * @param string           $method
     * @param string           $data
     * @param array            $headers
     * @param array            $options
     *
     * @group unit
     * @dataProvider getRequestData
     */
    public function testRequest($expectedResponse, $mockedProtocolHandlerResponse, $url, $method, $data, $headers, $options)
    {
        $mock = $this->mocked('testProtocolHandler', HttpProtocolHandlerInterface::class);
        $mock->expects($this->once())->method('request')->willReturn($mockedProtocolHandlerResponse);

        $this->s()->registerProtocolHandler('http', $mock, ['get']);
        $response = $this->s()->request($url, $method, $data, $headers, $options);

        $this->assertEquals($expectedResponse, $response);
    }
    /**
     * @return array
     */
    public function getRequestData()
    {
        return [
            [
                ['statusCode' => 200, 'status' => 'OK', 'content' => 'hello'],
                ['statusCode' => 200, 'status' => 'OK', 'content' => 'hello'],
                'http://thetesturl',
                'GET',
                null,
                [],
                [],
            ],
        ];
    }
    /**
     * @param array|\Exception $expectedResponse
     * @param array|\Exception $mockedProtocolHandlerResponse
     * @param string           $url
     * @param string           $method
     * @param string           $data
     * @param array            $headers
     * @param array            $options
     *
     * @group unit
     * @dataProvider getJsonRequestData
     */
    public function testJsonRequest($expectedResponse, $mockedProtocolHandlerResponse, $url, $method, $data, $headers, $options)
    {
        $mock = $this->mocked('testProtocolHandler', HttpProtocolHandlerInterface::class);
        $mock->expects($this->once())->method('request')->willReturn($mockedProtocolHandlerResponse);

        $this->s()->registerProtocolHandler('http', $mock, ['get']);
        $response = $this->s()->jsonRequest($url, $method, $data, $headers, $options);

        $this->assertEquals($expectedResponse, $response);
    }
    /**
     * @return array
     */
    public function getJsonRequestData()
    {
        return [
            [
                ['statusCode' => 200, 'status' => 'OK', 'content' => ['a' => 'b'], 'rawContent' => '{"a": "b"}'],
                ['statusCode' => 200, 'status' => 'OK', 'content' => '{"a": "b"}'],
                'http://thetesturl',
                'GET',
                null,
                [],
                [],
            ],
            [
                ['statusCode' => 200, 'status' => 'OK', 'content' => null, 'rawContent' => '{badly formatted'],
                ['statusCode' => 200, 'status' => 'OK', 'content' => '{badly formatted'],
                'http://thetesturl',
                'GET',
                null,
                [],
                [],
            ],
        ];
    }
}
