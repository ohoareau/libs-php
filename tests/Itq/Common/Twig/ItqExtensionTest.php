<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Twig;

use Itq\Common\Twig\ItqExtension;
use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group twig
 * @group twig/extensions
 * @group twig/extensions/itq
 */
class ItqExtensionTest extends AbstractTestCase
{
    /**
     * @return ItqExtension
     */
    public function e()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            ['a' => 'b', 'c' => 'd'],
            $this->mockedExceptionService(),
            $this->mockedTemplateService(),
            $this->mockedTokenStorage(),
            $this->mockedYamlService(),
        ];
    }
    /**
     * @group unit
     */
    public function testGetGlobals()
    {
        $this->assertEquals(['ws' => ['a' => 'b', 'c' => 'd']], $this->e()->getGlobals());
    }
    /**
     * @group unit
     */
    public function testGetFunctions()
    {
        $this->assertNotEquals(0, count($this->e()->getFunctions()));
    }
    /**
     * @group unit
     */
    public function testGetFilters()
    {
        $this->assertNotEquals(0, count($this->e()->getFilters()));
    }
    /**
     * @group unit
     */
    public function testGetTokenParsers()
    {
        $this->assertEquals(0, count($this->e()->getTokenParsers()));
    }
    /**
     * @group unit
     */
    public function testGetBase64EncodedString()
    {
        $this->assertEquals(base64_encode('test'), $this->e()->getBase64EncodedString('test'));
    }
    /**
     * @group unit
     */
    public function testGetName()
    {
        $this->assertEquals('itq', $this->e()->getName());
    }
}
