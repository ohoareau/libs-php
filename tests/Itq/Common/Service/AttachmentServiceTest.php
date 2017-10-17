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
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group  services
 * @group  services/attachment
 */
class AttachmentServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\AttachmentService
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
        return [$this->mockedGeneratorService(), $this->mockedFilesystemService()];
    }
    /**
     * @param array $definition
     * @param array $params
     * @param array $options
     * @param array $expected
     *
     * @group unit
     * @group attachment
     *
     * @dataProvider getBuildData
     */
    public function testBuild(array $definition, array $params, array $options, $expected)
    {
        if (isset($definition['generator'])) {
            $this->mockedGeneratorService()->expects($this->once())->method('generate')
                ->will($this->returnValue('I am generator'));
        } elseif (isset($definition['path'])) {
            $this->mockedFilesystemService()->expects($this->once())->method('readFile')
                ->will($this->returnValue('I am path'));
        }

        $this->assertEquals($expected, $this->s()->build($definition, $params, $options));
    }
    /**
     * @return array
     */
    public function getBuildData()
    {
        return [
            '0 - build from content' => [
                ['name' => 'test.pdf', 'content' => 'I am content'],
                [],
                [],
                ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('I am content'), ],
            ],
            '1 - build from generator success' => [
                ['name' => 'test.pdf', 'generator' => 'test'],
                [],
                [],
                ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('I am generator'), ],
            ],
            '2 - build from base64' => [
                ['name' => 'test.pdf', 'base64_content' => base64_encode('I am base64'), ],
                [],
                [],
                ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('I am base64'), ],
            ],
            '3 - build from path' => [
                ['name' => 'test.pdf', 'path' => 'path/test.pdf', ],
                [],
                [],
                ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('I am path'), ],
            ],
        ];
    }
    /**
     * @param array             $definition
     * @param array             $params
     * @param array             $options
     * @param \RuntimeException $exception
     *
     * @group unit
     * @group attachment
     *
     * @dataProvider getBuildException
     */
    public function testBuildThrowException(array $definition, array $params, array $options, $exception)
    {
        $this->expectExceptionThrown($exception);
        $this->s()->build($definition, $params, $options);
    }
    /**
     * @return array
     */
    public function getBuildException()
    {
        return [
            '0 - build with missing source throw exception' => [
                ['name' => 'fileNotExists.pdf'],
                [],
                [],
                new \RuntimeException('Missing source for attachment', 412),
            ],
        ];
    }
    /**
     * @param string $filename
     * @param string $expected
     *
     * @group        unit
     *
     * @dataProvider getMimeTypeFromFileNameData
     */
    public function testGetMimeTypeFromFileName($filename, $expected)
    {
        $m = $this->accessible($this->s(), 'getMimeTypeFromFileName');
        $this->assertEquals($expected, $m->invoke($this->s(), $filename));
    }
    /**
     * @return array
     */
    public function getMimeTypeFromFileNameData()
    {
        return [
            '0 - pdf' => ['file.pdf', 'application/pdf'],
            '1 - jpg' => ['file.jpg', 'image/jpeg'],
            '2 - png' => ['file.png', 'image/png'],
            '3 - gif' => ['file.gif', 'image/gif'],
            '4 - others is octect stream' => ['file.bin', 'application/octet-stream'],
        ];
    }
}
