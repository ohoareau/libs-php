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
        return [$this->mockedGeneratorService()];
    }

    /**
     * @param array $definition
     * @param array $params
     * @param array $options
     * @param       $expected
     *
     * @group        unit
     * @group        attachment
     *
     * @dataProvider getBuildData
     */
    public function testBuild(array $definition, array $params, array $options, $expected)
    {
        if (isset($definition['generator'])) {
            $this->mockedGeneratorService()->expects($this->once())->method('generate')->will(
                $this->returnValue('result')
            );
        }

        $this->assertEquals($expected, $this->s()->build($definition, $params, $options));
    }

    public function getBuildData()
    {
        return [
            '0 - build from content'           => [
                ['name' => 'test.pdf', 'content' => 'Je suis content'],
                [],
                [],
                ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('Je suis content')]
            ],
            '1 - build from generator success' => [
                ['name' => 'test.pdf', 'generator' => 'test'],
                [],
                [],
                ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('result')]
            ],
            '2 - build from base64'            => [
                ['name' => 'test.pdf', 'base64_content' => base64_encode('Je suis base64')],
                [],
                [],
                ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('Je suis base64')]
            ],
        ];
    }

    /**
     * @param array $definition
     * @param array $params
     * @param array $options
     * @param       $exception
     *
     * @group        unit
     * @group        attachment
     *
     * @dataProvider getBuildException
     */
    public function testBuildThrowException(array $definition, array $params, array $options, $exception)
    {
        $this->expectExceptionThrown($exception);
        $this->s()->build($definition, $params, $options);
    }

    public function getBuildException()
    {
        return [
            '0 - build with missing source throw exception' => [
                ['name' => 'fileNotExists.pdf'],
                [],
                [],
                new \RuntimeException('Missing source for attachment', 412)
            ],

            '1 - build from path with path not found throw exception' => [
                ['name' => 'fileNotExists.pdf', 'path' => 'fileNotExists.pdf'],
                [],
                [],
                new \RuntimeException('File \'fileNotExists.pdf\' not found', 404)
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

    public function getMimeTypeFromFileNameData()
    {
        return [
            '0 - pdf'                     => ['file.pdf', 'application/pdf'],
            '1 - jpg'                     => ['file.jpg', 'image/jpeg'],
            '2 - png'                     => ['file.png', 'image/png'],
            '3 - gif'                     => ['file.gif', 'image/gif'],
            '4 - others is octect stream' => ['file.bin', 'application/octet-stream'],
        ];
    }
}
