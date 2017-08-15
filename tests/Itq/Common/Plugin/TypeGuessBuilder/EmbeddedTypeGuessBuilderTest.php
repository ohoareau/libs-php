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
 * @group plugins/type-guess-builders/embedded
 */
class EmbeddedTypeGuessBuilderTest extends AbstractTypeGuessBuilderTestCase
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
                $this->tg('app_theType_create', [], Guess::HIGH_CONFIDENCE),
                [],
                ['class' => 'Class1', 'property' => 'property1', 'operation' => 'create'],
                [
                    'metaDataService' => [
                        ['getModelEmbeddedByProperty', ['Class1', 'property1'], ['type' => 'theType']],
                    ],
                ],
            ],
            [
                $this->tg('app_the_other_type_update', [], Guess::HIGH_CONFIDENCE),
                [],
                ['class' => 'Class2', 'property' => 'property2', 'operation' => 'update'],
                [
                    'metaDataService' => [
                        ['getModelEmbeddedByProperty', ['Class2', 'property2'], ['type' => 'the.other.type']],
                    ],
                ],
            ],
        ];
    }
}
