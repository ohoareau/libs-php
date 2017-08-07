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

use Itq\Common\ErrorManager;
use Itq\Common\Service\StringService;
use Itq\Common\Exception\ErrorException;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

use Symfony\Component\Translation\Translator;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/string
 */
class StringServiceTest extends AbstractServiceTestCase
{
    /**
     * @return StringService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     *
     * @param string $text
     * @param string $expected
     *
     * @dataProvider getRemoveStressesData
     */
    public function testRemoveStresses($text, $expected)
    {
        $this->assertEquals($expected, $this->s()->removeStresses($text));
    }
    /**
     * @return array
     */
    public function getRemoveStressesData()
    {
        return [
            ['a', 'a'],
            ['ab', 'ab'],
            ['aéb', 'aeb'],
            ['éèEÈ', 'eeEE'],
            ['àìZ t', 'aiZ t'],
            ['œ', 'oe'],
        ];
    }
    /**
     * @group unit
     *
     * @param string $message
     * @param string $expected
     *
     * @dataProvider getKeywordsData
     */
    public function testNormalizeKeywords($message, $expected)
    {
        $this->assertEquals($expected, $this->s()->normalizeKeyword($message));
    }
    /**
     * @return array
     */
    public function getKeywordsData()
    {
        return [
            ['DON', 'DON'],
            ['DONTEST', 'DONTEST'],
            ['DON TEST', 'DONTEST'],
            ['DON 50', 'DON50'],
            ['don dauphiné', 'DONDAUPHINE'],
            ['don cœur', 'DONCOEUR'],
            ['CàRà Mél', 'CARAMEL'],
            ["B\n A\n\t R", 'BAR'],
            [' ._-$%\/wOrD ', 'WORD'],
            ['سؽۺ۩AbA', 'ABA'],
        ];
    }
    /**
     * @group unit
     */
    public function testExceptionWithNoErrorManager()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage("service.method.unknown");

        /** @noinspection PhpUndefinedMethodInspection */
        $this->s()->unknownMethod();
    }
    /**
     * @group integ
     */
    public function testExceptionWithErrorManager()
    {
        $translator = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->setMethods(['trans'])->getMock();
        $errorManager = new ErrorManager($translator);
        $translator->expects($this->once())->method('trans')->with('service.method.unknown')->willReturn('This is a customized message');
        $this->s()->setErrorManager($errorManager);

        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->s()->unknownMethod();
        } catch (ErrorException $e) {
            $this->assertEquals(500, $e->getCode());
            $this->assertEquals(1001, $e->getApplicationCode());
            $this->assertEquals("This is a customized message", $e->getMessage());
        }
    }
    /**
     * @param int                      $nbLoopBeforeUnique
     * @param string                   $algo
     * @param string                   $prefix
     * @param string|\RuntimeException $expectedPattern
     * @param int                      $expectedLoopCount
     *
     * @group unit
     *
     * @dataProvider getGenerateUniqueCodeData
     */
    public function testGenerateUniqueCode($nbLoopBeforeUnique, $algo, $prefix, $expectedPattern, $expectedLoopCount)
    {
        $ctx = (object) ['loops' => 0];

        if ($expectedPattern instanceof \Exception) {
            $this->expectException(get_class($expectedPattern));
            $this->expectExceptionMessage($expectedPattern->getMessage());
            $this->expectExceptionCode($expectedPattern->getCode());
        }

        $exception = null;
        $value     = null;

        try {
            $value = $this->s()->generateUniqueCode(
                function () use ($nbLoopBeforeUnique, $ctx) {
                    $result = $ctx->loops < $nbLoopBeforeUnique;
                    $ctx->loops++;

                    return $result;
                },
                $algo,
                $prefix
            );
        } catch (\RuntimeException $e) {
            $exception = $e;
        }

        if (!($expectedPattern instanceof \Exception)) {
            $this->assertRegExp($expectedPattern, $value);
        }

        $this->assertEquals($expectedLoopCount, $ctx->loops);

        if ($exception) {
            throw $exception;
        }
    }
    /**
     * @return array
     */
    public function getGenerateUniqueCodeData()
    {
        return [
            [0, '5LD', null, '/^[A-Z0-9]{5}$/', 1],
            [0, '3L3D', 'GO', '/^GO[A-Z]{3}[0-9]{3}$/', 1],
            [1, '3L3D', 'TD', '/^TD[A-Z]{3}[0-9]{3}$/', 2],
            [2, '3L3D', 'TC', '/^TC[A-Z]{3}[0-9]{3}$/', 3],
            [1, '3l3d', 'm', '/^m[a-z]{3}[0-9]{3}$/', 2],
            [0, '4ld', null, '/^[a-z0-9]{4}$/', 1],
            [0, null, null, '/^[A-Z]{3}[0-9]{3}$/', 1],
            [11, null, null, new \RuntimeException("Too much iteration (11) for generating unique code", 412), 11],
        ];
    }
    /**
     * @param string $algo
     * @param string $prefix
     * @param string $expectedPattern
     *
     * @group unit
     *
     * @dataProvider getGenerateCodeData
     */
    public function testGenerateCode($algo, $prefix, $expectedPattern)
    {
        $value = $this->s()->generateCode($algo, $prefix);

        $this->assertRegExp($expectedPattern, $value);
    }
    /**
     * @return array
     */
    public function getGenerateCodeData()
    {
        return [
            ['2L1D', null, '/^[A-Z]{2}[0-9]{1}$/'],
            ['2L1D', 'ABCD:', '/^ABCD\:[A-Z]{2}[0-9]{1}$/'],
            ['5LD', null, '/^[A-Z0-9]{5}$/'],
            ['3L3D', 'GO', '/^GO[A-Z]{3}[0-9]{3}$/'],
            ['3L3D', 'TD', '/^TD[A-Z]{3}[0-9]{3}$/'],
            ['3L3D', 'TC', '/^TC[A-Z]{3}[0-9]{3}$/'],
            ['3l3d', 'm', '/^m[a-z]{3}[0-9]{3}$/'],
            ['4ld', null, '/^[a-z0-9]{4}$/'],
            [null, null, '/^[A-Z]{3}[0-9]{3}$/'],
        ];
    }
}
