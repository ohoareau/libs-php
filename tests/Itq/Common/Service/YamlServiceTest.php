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

use Itq\Common\Service\YamlService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/yaml
 */
class YamlServiceTest extends AbstractServiceTestCase
{
    /**
     * @return YamlService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @param mixed $expected
     * @param mixed $value
     * @param array $options
     *
     * @group integ
     *
     * @dataProvider getSerializeData
     */
    public function testSerialize($expected, $value, $options)
    {
        $this->assertEquals($expected, trim($this->s()->serialize($value, $options)));
    }
    /**
     * @return array
     */
    public function getSerializeData()
    {
        return [
            ['a: 12', ['a' => 12], []],
            ['a: null', ['a' => null], []],
        ];
    }
}
