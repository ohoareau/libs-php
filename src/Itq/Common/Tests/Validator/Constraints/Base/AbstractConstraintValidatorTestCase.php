<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Validator\Constraints\Base;

use Symfony\Component\Validator\Constraint;
use Itq\Common\Tests\Validator\Base\AbstractValidatorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractConstraintValidatorTestCase extends AbstractValidatorTestCase
{
    /**
     * @return Constraint
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
}
