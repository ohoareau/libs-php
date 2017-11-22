<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Service\RepositoryService;
use Itq\Common\Service\DatabaseServiceInterface;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/repository
 */
class RepositoryServiceTest extends AbstractServiceTestCase
{
    /**
     * @return RepositoryService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @param array|\Exception $expectedResult
     * @param string           $collectionName
     * @param string|array     $id
     * @param array            $data
     * @param array            $dbUpdateMockedCalls
     *
     * @group unit
     *
     * @dataProvider getUpdateData
     */
    public function testUpdate($expectedResult, $collectionName, $id, $data, $dbUpdateMockedCalls)
    {
        $db = $this->mocked('databaseService', DatabaseServiceInterface::class);

        $this->s()->setDatabaseService($db);
        $this->s()->setCollectionName($collectionName);
        if ($expectedResult instanceof \Exception) {
            $this->expectExceptionThrown($expectedResult);
        }
        foreach ($dbUpdateMockedCalls as $i => $dbUpdateMockedCall) {
            $db->expects($this->at($i))->method('update')->with($collectionName, is_array($id) ? $id : ['_id' => $id], $dbUpdateMockedCall[0])->willReturn($dbUpdateMockedCall[1]);
        }

        $result = $this->s()->update($id, $data);

        if (!($expectedResult instanceof \Exception)) {
            $this->assertEquals($expectedResult, $result);
        }
    }
    /**
     * @return array
     */
    public function getUpdateData()
    {
        return [
            '0 - basic' => [
                [], 'type1', 'the_id', ['a' => 'b'], [[['$set' => ['a' => 'b']], []]],
            ],
            '1 - no data to update' => [
                new \RuntimeException('doc.update.empty/type1', 412), 'type1', 'the_id', [], [],
            ],
            '2 - sub key' => [
                [], 'type1', 'the_id', ['a.b' => 'c', 'z' => 't', 'd.e.f' => 4], [[['$set' => ['a.b' => 'c', 'z' => 't', 'd.e.f' => 4]], []]],
            ],
            '3 - toggle (add)' => [
                [], 'type1', 'the_id', ['a:toggle' => 'zz'], [[['$addToSet' => ['a' => ['$each' => ['zz']]]], []]],
            ],
            '4 - toggle (add multiple)' => [
                [], 'type1', 'the_id', ['a:toggle' => 'zz,tt,xx'], [[['$addToSet' => ['a' => ['$each' => ['zz', 'tt', 'xx']]]], []]],
            ],
            '5 - toggle (add) + set' => [
                [], 'type1', 'the_id', ['x' => true, 'b.c:toggle' => 'ee', 'y' => 'n'], [[['$set' => ['x' => true, 'y' => 'n'], '$addToSet' => ['b.c' => ['$each' => ['ee']]]], []]],
            ],
            '6 - toggle (remove)' => [
                [], 'type1', 'the_id', ['a:toggle' => '!zz'], [[['$pull' => ['a' => ['$in' => ['zz']]]], []]],
            ],
            '7 - toggle (remove multiple)' => [
                [], 'type1', 'the_id', ['a:toggle' => '!zz,!qq'], [[['$pull' => ['a' => ['$in' => ['zz', 'qq']]]], []]],
            ],
            '8 - toggle (remove) + set' => [
                [], 'type1', 'the_id', ['x' => true, 'b.c:toggle' => '!ee', 'y' => 'n'], [[['$set' => ['x' => true, 'y' => 'n'], '$pull' => ['b.c' => ['$in' => ['ee']]]], []]],
            ],
            '9 - unknown modifier' => [
                [], 'type1', 'the_id', ['x:unknown' => 15, 'y' => 'n'], [[['$set' => ['x:unknown' => 15, 'y' => 'n']], []]],
            ],
            '10 - toggle (add) + toggle (remove)' => [
                [], 'type1', 'the_id', ['x' => true, 'b.c:toggle' => 'ee', 'ee:toggle' => '!ee', 'y' => 'n'], [[['$set' => ['x' => true, 'y' => 'n'], '$addToSet' => ['b.c' => ['$each' => ['ee']]]], []], [['$pull' => ['ee' => ['$in' => ['ee']]]], []]],
            ],
        ];
    }
}
