<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @Annotation
 */
class ValidationBusinessRules extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'app_validation_business_rules';
    }
    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
