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
 * @group plugins/type-guess-builders/workflow
 */
class WorkflowTypeGuessBuilderTest extends AbstractTypeGuessBuilderTestCase
{
    /**
     * @return array
     */
    public function getBuildData()
    {
        return [
            [$this->tg('choice', ['choices' => ['a' => 'a', 'b' => 'b'], 'choices_as_values' => false], Guess::HIGH_CONFIDENCE), ['steps' => ['a', 'b']]],
        ];
    }
}
