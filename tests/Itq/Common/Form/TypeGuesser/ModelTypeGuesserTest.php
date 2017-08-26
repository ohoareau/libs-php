<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Form\TypeGuesser;

use Itq\Common\Form\TypeGuesser\ModelTypeGuesser;
use Itq\Common\Tests\Form\TypeGuesser\Base\AbstractTypeGuesserFormTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group forms
 * @group forms/type-guessers
 * @group forms/type-guessers/model
 */
class ModelTypeGuesserTest extends AbstractTypeGuesserFormTestCase
{
    /**
     * @return ModelTypeGuesser
     */
    public function g()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::g();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedMetaDataService(), $this->mockedTypeGuessService()];
    }
}
