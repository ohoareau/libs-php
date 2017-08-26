<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Validator\Constraints;

use Itq\Common\Validator\Constraints\ValidationBusinessRules;
use Itq\Common\Tests\Validator\Constraints\Base\AbstractConstraintValidatorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group validators
 * @group validators/constraints
 * @group validators/constraints/business-rules
 */
class ValidationBusinessRulesTest extends AbstractConstraintValidatorTestCase
{
    /**
     * @return ValidationBusinessRules
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [];
    }
}
