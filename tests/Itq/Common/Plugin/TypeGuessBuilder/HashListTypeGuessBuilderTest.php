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
 * @group plugins/type-guess-builders/hash-list
 */
class HashListTypeGuessBuilderTest extends AbstractTypeGuessBuilderTestCase
{
    /**
     * @return array
     */
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
                $this->tg('collection', ['type' => 'app_boolean', 'allow_add' => true, 'allow_delete' => true], Guess::HIGH_CONFIDENCE),
                [],
                ['class' => 'Class1', 'property' => 'property1'],
                [
                    'metaDataService' => [
                        ['getModelHashListByProperty', ['Class1', 'property1'], ['type' => 'bool']],
                    ],
                ],
            ],
            [
                $this->tg('collection', ['type' => 'text', 'allow_add' => true, 'allow_delete' => true], Guess::HIGH_CONFIDENCE),
                [],
                ['class' => 'Class2', 'property' => 'property2'],
                [
                    'metaDataService' => [
                        ['getModelHashListByProperty', ['Class2', 'property2'], ['type' => 'other']],
                    ],
                ],
            ],
        ];
    }
}
