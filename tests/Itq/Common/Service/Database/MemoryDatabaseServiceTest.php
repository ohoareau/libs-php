<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service\Database;

use Itq\Common\Service\Database\MemoryDatabaseService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/databases
 * @group services/databases/memory
 */
class MemoryDatabaseServiceTest extends AbstractServiceTestCase
{
    /**
     * @return MemoryDatabaseService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mockedCriteriumService(),
            $this->mockedConnectionService(),
            $this->mockedEventDispatcher(),
        ];
    }
}
