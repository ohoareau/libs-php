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

use Itq\Common\Tests\Validator\Base\AbstractValidatorTestCase;
use Symfony\Component\Validator\ConstraintValidatorInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractValidatorValidatorTestCase extends AbstractValidatorTestCase
{
    /**
     * @return ConstraintValidatorInterface
     */
    public function v()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
}
