<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\CodeGenerator\Sdk\Crud;

use Itq\Common\Plugin\CodeGenerator\Sdk\Crud\FindCrudSdkCodeGenerator;
use Itq\Common\Tests\Plugin\CodeGenerator\Sdk\Base\AbstractSdkCodeGeneratorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/code-generators
 * @group plugins/code-generators/sdks
 * @group plugins/code-generators/sdks/cruds
 * @group plugins/code-generators/sdks/cruds/find
 */
class FindCrudSdkCodeGeneratorTest extends AbstractSdkCodeGeneratorTestCase
{
    /**
     * @return FindCrudSdkCodeGenerator
     */
    public function o()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [];
    }
}
