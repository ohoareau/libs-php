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

use Itq\Common\Plugin\ExceptionDescriptor\UnsupportedAccountTypeExceptionExceptionDescriptor;
use Itq\Common\Tests\Plugin\ExceptionDescriptor\Base\AbstractExceptionDescriptorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/exception-descriptors
 * @group plugins/exception-descriptors/unsupported-account-type-exception
 */
class UnsupportedAccountTypeExceptionExceptionDescriptorTest extends AbstractExceptionDescriptorTestCase
{
    /**
     * @return UnsupportedAccountTypeExceptionExceptionDescriptor
     */
    public function d()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::d();
    }
}
