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

use Itq\Common\Service\ArchiverService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/archiver
 */
class ArchiverServiceTest extends AbstractServiceTestCase
{
    /**
     * @return ArchiverService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testRegister()
    {
        $callback = function () {
        };

        $this->s()->register('test', $callback);

        $this->assertEquals(['type' => 'callable', 'callable' => $callback, 'options' => []], $this->s()->get('test'));
    }
    /**
     * @param string $type
     * @param array  $data
     * @param array  $options
     * @param array  $expected
     *
     * @group unit
     *
     * @dataProvider getArchiveData
     */
    public function testArchive($type, $data, $options, $expected)
    {
        $callback = function ($data) {
            return json_encode($data);
        };

        $this->s()->register('test', $callback);
        $this->assertEquals($expected, $this->s()->archive($type, $data, $options));
    }
    /**
     * @return array
     */
    public function getArchiveData()
    {
        return [
            '0 - success' => ['test', ['data1', 12, 'key' => 'val'], [], '{"0":"data1","1":12,"key":"val"}'],
        ];
    }
}
