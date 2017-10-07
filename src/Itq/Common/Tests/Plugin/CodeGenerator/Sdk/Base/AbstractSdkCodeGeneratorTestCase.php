<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\CodeGenerator\Sdk\Base;

use Itq\Common\Plugin\CodeGenerator\Sdk\Base\AbstractSdkCodeGenerator;
use Itq\Common\Tests\Plugin\CodeGenerator\Base\AbstractCodeGeneratorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractSdkCodeGeneratorTestCase extends AbstractCodeGeneratorTestCase
{
    /**
     * @return AbstractSdkCodeGenerator
     */
    public function o()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
}
