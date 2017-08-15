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
 * @group plugins/type-guess-builders/tag-list
 */
class TagListTypeGuessBuilderTest extends AbstractTypeGuessBuilderTestCase
{
    /**
     * @return array
     */
    public function getBuildData()
    {
        return [
            [$this->tg('collection', ['type' => 'text', 'allow_add' => true, 'allow_delete' => true], Guess::HIGH_CONFIDENCE)],
        ];
    }
}
