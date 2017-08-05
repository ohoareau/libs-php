<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common;

use Itq\Common\ErrorManager;
use Itq\Common\Exception\ErrorException;

use Itq\Common\Tests\Base\AbstractTestCase;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * @author itiQiti Dev Team <cto@itiqiti.com>
 *
 * @group objects
 * @group objects/error-manager
 */
class ErrorManagerTest extends AbstractTestCase
{
    /**
     * @group unit
     */
    public function testCreateExceptionWithNoTranslator()
    {
        $errorManager = new ErrorManager();

        /** @var ErrorException $e */
        $e = $errorManager->createException('a');

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('a', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(0, $e->getApplicationCode());
        $this->assertEquals('a', $e->getApplicationKey());
        $this->assertEquals([], $e->getApplicationParams());
    }
    /**
     * @group unit
     */
    public function testCreateExceptionForNotTranslated()
    {
        $translator = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->setMethods(['trans'])->getMock();
        $errorManager = new ErrorManager($translator);
        $translator->expects($this->once())->method('trans')->with('a')->willReturn('a');

        /** @var ErrorException $e */
        $e = $errorManager->createException('a');

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('a', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(0, $e->getApplicationCode());
        $this->assertEquals('a', $e->getApplicationKey());
        $this->assertEquals([], $e->getApplicationParams());
    }
    /**
     * @group unit
     */
    public function testCreateExceptionForTranslated()
    {
        $translator = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->setMethods(['trans'])->getMock();
        $errorManager = new ErrorManager($translator);
        $translator->expects($this->once())->method('trans')->with('a')->willReturn('b');

        /** @var ErrorException $e */
        $e = $errorManager->createException('a');

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('b', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(0, $e->getApplicationCode());
        $this->assertEquals('a', $e->getApplicationKey());
        $this->assertEquals([], $e->getApplicationParams());
    }
    /**
     * @group unit
     */
    public function testCreateExceptionForTranslatedWithNamedParams()
    {
        $translator = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->setMethods(['trans'])->getMock();
        $errorManager = new ErrorManager($translator);
        $translator->expects($this->once())->method('trans')->with('a', ['%x%' => 'y', '%z%' => 't'])->willReturn('b');

        /** @var ErrorException $e */
        $e = $errorManager->createException('a', [['%x%' => 'y', '%z%' => 't']]);

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('b', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(0, $e->getApplicationCode());
        $this->assertEquals('a', $e->getApplicationKey());
        $this->assertEquals(['x' => 'y', 'z' => 't'], $e->getApplicationParams());
    }
    /**
     * @group integ
     */
    public function testCreateExceptionForRealTranslatorWithoutTranslation()
    {
        $translator = new Translator('fr_FR');
        $errorManager = new ErrorManager($translator);

        /** @var ErrorException $e */
        $e = $errorManager->createException('service.method.unknown');

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('service.method.unknown', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(0, $e->getApplicationCode());
        $this->assertEquals('service.method.unknown', $e->getApplicationKey());
        $this->assertEquals([], $e->getApplicationParams());
    }
    /**
     * @group integ
     */
    public function testCreateExceptionForRealTranslatorWithNoTranslation()
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('yml', new YamlFileLoader());
        $errorManager = new ErrorManager($translator);

        /** @var ErrorException $e */

        // fr_FR (default)
        $e = $errorManager->createException('service.method.unknown', ['A', 'b']);

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('service.method.unknown', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(0, $e->getApplicationCode());
        $this->assertEquals('service.method.unknown', $e->getApplicationKey());
        $this->assertEquals(['A', 'b'], $e->getApplicationParams());

    }
}
