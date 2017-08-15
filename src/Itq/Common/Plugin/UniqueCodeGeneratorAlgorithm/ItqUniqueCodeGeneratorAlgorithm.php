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

use Itq\Common\Plugin\Base\AbstractPlugin;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ItqUniqueCodeGeneratorAlgorithm extends AbstractPlugin
{
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("5LD")
     */
    public function algo5UpperCaseLettersAndDigits()
    {
        $chars = [];

        for ($k = 0; $k < 5; $k++) {
            $chars[] = rand(0, 1) === 1 ? chr(65 + rand(0, 25)) : ((string) rand(0, 9));
        }

        shuffle($chars);

        return join('', $chars);
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("4ld")
     */
    public function algo4LowerCaseLettersAndDigits()
    {
        $chars = [];

        for ($k = 0; $k < 4; $k++) {
            $chars[] = rand(0, 1) === 1 ? chr(65 + rand(0, 25)) : ((string) rand(0, 9));
        }

        shuffle($chars);

        return strtolower(join('', $chars));
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("3l3d")
     */
    public function algo3LowerCaseLettersAnd3Digits()
    {
        return strtolower(
            chr(65 + rand(0, 25))
            .chr(65 + rand(0, 25))
            .chr(65 + rand(0, 25))
            .(string) rand(0, 9)
            .(string) rand(0, 9)
            .(string) rand(0, 9)
        );
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("2L1D")
     */
    public function algo2UpperCaseLettersAnd1Digit()
    {
        return chr(65 + rand(0, 25)).chr(65 + rand(0, 25)).(string) rand(0, 9);
    }
    /**
     * @Annotation\UniqueCodeGeneratorAlgorithm("3L3D")
     */
    public function algo3UpperCaseLettersAnd3Digits()
    {
        return chr(65 + rand(0, 25))
            .chr(65 + rand(0, 25))
            .chr(65 + rand(0, 25))
            .(string) rand(0, 9)
            .(string) rand(0, 9)
            .(string) rand(0, 9)
        ;
    }
}
