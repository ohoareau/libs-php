<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\UniqueCodeGeneratorAlgorithm;

use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ItqUniqueCodeGeneratorAlgorithm extends Base\AbstractUniqueCodeGeneratorAlgorithm
{
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("5LD")
     *
     * @return string
     */
    public function algo5UpperCaseLettersAndDigits()
    {
        return $this->shuffledCharsOrDigits(5, 'upper');
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("5ld")
     *
     * @return string
     */
    public function algo5LettersAndDigitsWithDefaultMode()
    {
        return $this->charOrDigit(5);
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("4ld")
     *
     * @return string
     */
    public function algo4LowerCaseLettersAndDigits()
    {
        return $this->shuffledCharsOrDigits(4, 'lower');
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("3l3d")
     *
     * @return string
     */
    public function algo3LowerCaseLettersAnd3Digits()
    {
        return $this->char(3, 'lower').$this->digit(3);
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("2L1D")
     *
     * @return string
     */
    public function algo2UpperCaseLettersAnd1Digit()
    {
        return $this->char(2, 'upper').$this->digit(1);
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("3L3D")
     *
     * @return string
     */
    public function algo3UpperCaseLettersAnd3Digits()
    {
        return $this->char(3, 'upper').$this->digit(3);
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("8Lld")
     *
     * @return string
     */
    public function algo8LettersAndDigits()
    {
        return $this->charOrDigit(8, 'both');
    }
}
