<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Generator;

use Itq\Common\Plugin\Generator\StringGenerator;
use Itq\Common\Tests\Plugin\Generator\Base\AbstractGeneratorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/generators
 * @group plugins/generators/string
 */
class StringGeneratorTest extends AbstractGeneratorTestCase
{
    /**
     * @return StringGenerator
     */
    public function g()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::g();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mockedStringService(),
            'desktop',
            null,
        ];
    }
    /**
     * @group unit
     */
    public function testGenerateMd5()
    {
        $this->assertEquals(32, strlen($this->g()->generateRandomMd5String()));
        $this->assertTrue(0 < preg_match('/^[a-f0-9]+$/', $g = $this->g()->generateRandomMd5String()), sprintf("string '%s' is not valid md5 string", $g));
    }
    /**
     * @group unit
     */
    public function testGenerateSha1()
    {
        $this->assertEquals(40, strlen($this->g()->generateRandomSha1String()));
    }
    /**
     * @group unit
     */
    public function testGetStorageUrlPatternVars()
    {
        $this->g()->setStorageUrlPattern('http://abc.com/{a}z{b}z{dE}/{z}');

        $this->assertEquals('http://abc.com/{a}z{b}z{dE}/{z}', $this->g()->getStorageUrlPattern());
        $this->assertEquals(['{a}' => true, '{b}' => true, '{dE}' => true, '{z}' => true], $this->g()->getStorageUrlPatternVars());
    }
}
