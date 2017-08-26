<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Form\TypeGuesser\Base;

use Itq\Common\Tests\Form\Base\AbstractFormTestCase;
use Symfony\Component\Form\FormTypeGuesserInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTypeGuesserFormTestCase extends AbstractFormTestCase
{
    /**
     * @return FormTypeGuesserInterface
     */
    public function g()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
}
