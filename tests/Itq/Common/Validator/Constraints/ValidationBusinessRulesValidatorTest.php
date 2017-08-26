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

use Itq\Common\Validator\Constraints\ValidationBusinessRulesValidator;
use Itq\Common\Tests\Validator\Constraints\Base\AbstractValidatorValidatorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group validators
 * @group validators/validators
 * @group validators/validators/business-rules
 */
class ValidationBusinessRulesValidatorTest extends AbstractValidatorValidatorTestCase
{
    /**
     * @return ValidationBusinessRulesValidator
     */
    public function v()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::v();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedBusinessRuleService(), $this->mockedMetaDataService(), $this->mockedErrorManager()];
    }
}
