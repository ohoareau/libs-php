<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Migrator;

use Itq\Common\Plugin\Migrator\ShMigrator;
use Itq\Common\Tests\Plugin\Migrator\Base\AbstractMigratorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/migrators
 * @group plugins/migrators/sh
 */
class ShMigratorTest extends AbstractMigratorTestCase
{
    /**
     * @return ShMigrator
     */
    public function m()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::m();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedSystemService(), $this->mockedFilesystemService()];
    }
}
