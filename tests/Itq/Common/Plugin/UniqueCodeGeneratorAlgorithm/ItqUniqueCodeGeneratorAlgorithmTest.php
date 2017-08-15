<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\UniqueCodeGeneratorAlgorithm;

use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;
use Itq\Common\Plugin\UniqueCodeGeneratorAlgorithm\ItqUniqueCodeGeneratorAlgorithm;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/unique-code-generator-algorithms
 * @group plugins/unique-code-generator-algorithms/itq
 */
class ItqUniqueCodeGeneratorAlgorithmTest extends AbstractPluginTestCase
{
    /**
     * @return ItqUniqueCodeGeneratorAlgorithm
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
    /**
     * @param string $algoMethod
     * @param string $expectedPattern
     * @param int    $loops
     *
     * @group unit
     * @dataProvider getAlgoData
     */
    public function testAlgo($algoMethod, $expectedPattern, $loops = 4)
    {
        for ($i = 0; $i < $loops; $i++) {
            $this->assertRegExp($expectedPattern, $this->p()->$algoMethod());
        }
    }
    /**
     * @return array
     */
    public function getAlgoData()
    {
        return [
            ['algo2UpperCaseLettersAnd1Digit',       '/^[A-Z]{2}[0-9]{1}$/'],
            ['algo5UpperCaseLettersAndDigits',       '/^[A-Z0-9]{5}$/'],
            ['algo3UpperCaseLettersAnd3Digits',      '/^[A-Z]{3}[0-9]{3}$/'],
            ['algo3LowerCaseLettersAnd3Digits',      '/^[a-z]{3}[0-9]{3}$/'],
            ['algo4LowerCaseLettersAndDigits',       '/^[a-z0-9]{4}$/'],
            ['algo8LettersAndDigits',                '/^[a-zA-Z0-9]{8}$/'],
            ['algo5LettersAndDigitsWithDefaultMode', '/^[a-z0-9]{5}$/'],
        ];
    }
}
