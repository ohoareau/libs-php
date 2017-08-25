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

use Itq\Common\Service\Database\MongoDatabaseService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/databases
 * @group services/databases/mongo
 */
class MongoDatabaseServiceTest extends AbstractServiceTestCase
{
    /**
     * @return MongoDatabaseService
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
            $this->mockedStorageService(),
            $this->mockedGeneratorService(),
        ];
    }
    /**
     * @param array $fields
     * @param array $expected
     *
     * @dataProvider getBuildFieldsData
     */
    public function testBuildFields($fields, $expected)
    {
        $this->assertEquals($expected, $this->s()->buildFields($fields));
    }
    /**
     * @return array
     */
    public function getBuildFieldsData()
    {
        return [
            [['a', 'b', 'c'], ['a' => true, 'b' => true, 'c' => true]],
            [['a' => true, 'b', 'c' => true], ['a' => true, 'b' => true, 'c' => true]],
            [['a', 'b', 'a.c'], ['a' => true, 'b' => true]],
            [['a.c', 'b', 'a'], ['a' => true, 'b' => true]],
        ];
    }
}
