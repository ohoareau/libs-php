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

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
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
    /**
     * @group integ
     */
    public function testCreateExceptionForRealTranslatorWithTranslation()
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('yml', new YamlFileLoader());
        $translator->addResource('yml', __DIR__.'/../../../src/Itq/Bundle/ItqBundle/Resources/translations/errors.fr.yml', 'fr_FR', 'errors');
        $translator->addResource('yml', __DIR__.'/../../../src/Itq/Bundle/ItqBundle/Resources/translations/errors.en.yml', 'en_US', 'errors');
        $errorManager = new ErrorManager($translator);

        /** @var ErrorException $e */

        // fr_FR (default)
        $e = $errorManager->createException('service.method.unknown', ['A', 'b']);

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('Méthode A::b() non disponible', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(0, $e->getApplicationCode());
        $this->assertEquals('service.method.unknown', $e->getApplicationKey());
        $this->assertEquals(['A', 'b'], $e->getApplicationParams());

        // en_US
        $translator->setLocale('en_US');

        $e = $errorManager->createException('service.method.unknown', ['A', 'b']);

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('Unknown method A::b()', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(0, $e->getApplicationCode());
        $this->assertEquals('service.method.unknown', $e->getApplicationKey());
        $this->assertEquals(['A', 'b'], $e->getApplicationParams());

    }
    /**
     * @group integ
     */
    public function testCreateExceptionForRealTranslatorWithMissingTranslation()
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('yml', new YamlFileLoader());
        $translator->addResource('yml', __DIR__.'/../../../src/Itq/Bundle/ItqBundle/Resources/translations/errors.en.yml', 'en_US', 'errors');
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
    /**
     * @group integ
     */
    public function testCreateExceptionForRealTranslatorWithTranslationAndAppCode()
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('yml', new YamlFileLoader());
        $translator->addResource('yml', __DIR__.'/../../../src/Itq/Bundle/ItqBundle/Resources/translations/errors.fr.yml', 'fr_FR', 'errors');
        $errorManager = new ErrorManager($translator);

        /** @var ErrorException $e */

        // fr_FR (default)
        $e = $errorManager->createException('#1010:service.method.unknown', ['A', 'b']);

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('Méthode A::b() non disponible', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(1010, $e->getApplicationCode());
        $this->assertEquals('service.method.unknown', $e->getApplicationKey());
        $this->assertEquals(['A', 'b'], $e->getApplicationParams());

    }
    /**
     * @group integ
     */
    public function testCreateExceptionForRealTranslatorWithTranslationAndMappedAppCode()
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('yml', new YamlFileLoader());
        $translator->addResource('yml', __DIR__.'/../../../src/Itq/Bundle/ItqBundle/Resources/translations/errors.fr.yml', 'fr_FR', 'errors');
        $errorManager = new ErrorManager($translator);
        $errorManager->setKeyCodeMapping(['service.method.unknown' => 1111]);

        /** @var ErrorException $e */

        // fr_FR (default)
        $e = $errorManager->createException('service.method.unknown', ['A', 'b']);

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('Méthode A::b() non disponible', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(1111, $e->getApplicationCode());
        $this->assertEquals('service.method.unknown', $e->getApplicationKey());
        $this->assertEquals(['A', 'b'], $e->getApplicationParams());

        $e = $errorManager->createException('#22:service.method.unknown', ['A', 'b']);

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('Méthode A::b() non disponible', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(22, $e->getApplicationCode());
        $this->assertEquals('service.method.unknown', $e->getApplicationKey());
        $this->assertEquals(['A', 'b'], $e->getApplicationParams());

    }
    /**
     * @group integ
     */
    public function testCreateExceptionForRealTranslatorWithTranslationAndRealMappedAppCode()
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('yml', new YamlFileLoader());
        $translator->addResource('yml', __DIR__.'/../../../src/Itq/Bundle/ItqBundle/Resources/translations/errors.fr.yml', 'fr_FR', 'errors');
        $errorManager = new ErrorManager($translator);
        $errorManager->setKeyCodeMapping(Yaml::parse(file_get_contents(__DIR__.'/../../../src/Itq/Bundle/ItqBundle/Resources/config/error-mapping.yml')));

        /** @var ErrorException $e */

        // fr_FR (default)
        $e = $errorManager->createException('service.method.unknown', ['A', 'b']);

        $this->assertTrue($e instanceof ErrorException);
        $this->assertEquals('Méthode A::b() non disponible', $e->getMessage());
        $this->assertEquals(500, $e->getCode());
        $this->assertEquals(11010, $e->getApplicationCode());
        $this->assertEquals('service.method.unknown', $e->getApplicationKey());
        $this->assertEquals(['A', 'b'], $e->getApplicationParams());
    }
}
