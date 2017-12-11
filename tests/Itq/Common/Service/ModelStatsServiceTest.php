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

use Itq\Common\Service\DocumentService;
use Itq\Common\Service\ModelStatsService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/model-stats
 */
class ModelStatsServiceTest extends AbstractServiceTestCase
{
    /**
     * @return ModelStatsService
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
        return [$this->mockedMetaDataService(), $this->mockedCrudService(), $this->mockedExpressionService()];
    }
    /**
     * @group unit
     */
    public function testTrack()
    {
        $definition = [
            'game' =>
                [
                    [
                        'key'       => 'payments',
                        'match'     => 'game',
                        'increment' => null,
                        'decrement' => null,
                        'type'      => null,
                        'formula'   => null,
                    ],
                    [
                        'key'       => 'completion',
                        'match'     => 'game',
                        'increment' => null,
                        'decrement' => null,
                        'type'      => 'double',
                        'formula'   => '($turnover / :capitalIssuance) >= 1 ? 1 : ($turnover / :capitalIssuance))',
                    ],
                ],
        ];
        $data = (object) ['id' => 'id-98', 'name' => 'toto', 'game' => 'gid-01'];

        $this->mockMethod($this->mocked('DocumentService', DocumentService::class), 'getRepository', null, $this->mockedRepositoryService());
        $this->mockMethod($this->mockedCrudService(), 'get', 'game', $this->mocked('DocumentService'));
        $this->s()->track($definition, $data);
    }
    /**
     * @group unit
     */
    public function testComputeTargetRepoTrackerValue()
    {
        $object = (object) ['id' => 'id-98', 'name' => 'toto', 'game' => 'gid-01'];
        $def = [
            'key'       => 'completion',
            'match'     => 'game',
            'increment' => null,
            'decrement' => null,
            'type'      => 'double',
            'formula'   => '($turnover / :capitalIssuance) >= 1 ? 1 : ($turnover / :capitalIssuance))',
        ];
        $ctx = (object) ['fetched' => true, 'otherSideFields' => [], 'fields' => []];

        $actual = $this->invokeProtected(
            'computeTargetRepoTrackerValue',
            $this->mockedRepositoryService(),
            $object,
            $def,
            $ctx,
            []
        );
        $this->assertCount(4, $actual);
        $this->assertTrue(is_callable($actual[0]));
        array_shift($actual);
        $def['replace'] = true;
        $this->assertEquals(['game', 'gid-01', $def], $actual);
    }
    /**
     * @group unit
     */
    public function testPopulate()
    {
        $doc = (object) ['id' => 'id-98', 'name' => 'toto', 'stats' => null, 'game' => null];
        $fields = ['id' => true, 'stats.turnover' => true, 'game' => true];
        $this->mockMethod($this->mockedMetaDataService(), 'getModel', $doc, ['id' => 'game']);
        $this->mockMethod(
            $this->mockedCrudService(),
            'get',
            'game',
            $this->mocked('DocumentService', DocumentService::class)
        );
        $this->mockMethod($this->mocked('DocumentService'), 'getExpectedTypeCount', null, 1);
        $this->mockMethod(
            $this->mocked('DocumentService'),
            'get',
            ['id-98', $fields, ['cached' => true, 'force' => false]],
            (object) ['id' => 'id-98', 'stats' => ['turnover' => 10], 'game' => 'game-id-02']
        );
        $actual = $this->invokeProtected('populate', $doc, 'id-98', $fields);
        $this->assertEqualsResultSet($actual);
    }
}
