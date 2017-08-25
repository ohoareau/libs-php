<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Generator;

use Itq\Common\Plugin\Generator\PasswordGenerator;
use Itq\Common\Tests\Plugin\Generator\Base\AbstractGeneratorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/generators
 * @group plugins/generators/password
 */
class PasswordGeneratorTest extends AbstractGeneratorTestCase
{
    /**
     * @return PasswordGenerator
     */
    public function g()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::g();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedPasswordService(), $this->mockedVaultService()];
    }
}
