<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ExceptionDescriptor;

use Itq\Common\Plugin\ExceptionDescriptor\NamedBusinessRuleExceptionExceptionDescriptor;
use Itq\Common\Tests\Plugin\ExceptionDescriptor\Base\AbstractExceptionDescriptorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/exception-descriptors
 * @group plugins/exception-descriptors/named-business-rule-exception
 */
class NamedBusinessRuleExceptionExceptionDescriptorTest extends AbstractExceptionDescriptorTestCase
{
    /**
     * @return NamedBusinessRuleExceptionExceptionDescriptor
     */
    public function d()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::d();
    }
}
