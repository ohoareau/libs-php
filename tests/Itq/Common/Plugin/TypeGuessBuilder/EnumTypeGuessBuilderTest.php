<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\TypeGuessBuilder;

use Itq\Common\Tests\Plugin\TypeGuessBuilder\Base\AbstractTypeGuessBuilderTestCase;

use Symfony\Component\Form\Guess\Guess;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/type-guess-builders
 * @group plugins/type-guess-builders/enum
 */
class EnumTypeGuessBuilderTest extends AbstractTypeGuessBuilderTestCase
{
    public function constructor()
    {
        return [$this->mockedMetaDataService()];
    }
    /**
     * @return array
     */
    public function getBuildData()
    {
        return [
            [
                $this->tg('choice', ['choices' => ['a' => 'a', 'b' => 'b'], 'choices_as_values' => false], Guess::HIGH_CONFIDENCE),
                ['values' => ['a', 'b']],
            ],
            [
                $this->tg('choice', ['choices' => [], 'choices_as_values' => false], Guess::HIGH_CONFIDENCE),
                ['values' => 'not_an_array'],
            ],
            [
                $this->tg('choice', ['choices' => ['x' => 'x', 'y' => 'y'], 'choices_as_values' => false], Guess::HIGH_CONFIDENCE),
                ['values' => '@reference_array'],
                [],
                [
                    'metaDataService' => [
                        ['getEnumValuesByType', ['reference_array'], ['x', 'y']],
                    ],
                ],
            ],
        ];
    }
}
