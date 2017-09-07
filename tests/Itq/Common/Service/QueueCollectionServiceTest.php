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

use RuntimeException;
use Itq\Common\Plugin;
use Itq\Common\Service\QueueCollectionService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/queue-collection
 */
class QueueCollectionServiceTest extends AbstractServiceTestCase
{
    /**
     * @return QueueCollectionService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @param string $type
     * @param string $pluginClass
     * @param array  $methods
     * @param string $getter
     * @param string $adder
     * @param string $optionalTypeForAdder
     * @param string $optionalSingleGetter
     * @param string $optionalGroupGetter
     *
     * @group unit
     *
     * @dataProvider getPluginsData
     */
    public function testPlugins($type, $pluginClass, array $methods, $getter, $adder, $optionalTypeForAdder = null, $optionalSingleGetter = null, $optionalGroupGetter = null)
    {
        $this->handleTestPlugins($type, $pluginClass, $methods, $getter, $adder, $optionalTypeForAdder, $optionalSingleGetter, $optionalGroupGetter);
    }
    /**
     * @return array
     */
    public function getPluginsData()
    {
        return [
            ['dataProvider', Plugin\QueueCollectionTypeInterface::class, ['create'], 'getQueueCollectionTypes', 'addQueueCollectionType', 'thetype', 'getQueueCollectionType'],
        ];
    }
    /**
     * @group unit
     */
    public function testCreate()
    {
        $queueCollection1 = new Plugin\QueueCollection\MemoryQueueCollection();
        $queueCollection2 = new Plugin\QueueCollection\MemoryQueueCollection();
        $queueCollection3 = new Plugin\QueueCollection\MemoryQueueCollection();

        $mock1 = $this->mocked('queueCollectionType1', Plugin\QueueCollectionTypeInterface::class);
        $mock2 = $this->mocked('queueCollectionType2', Plugin\QueueCollectionTypeInterface::class);
        $mock3 = $this->mocked('queueCollectionType3', Plugin\QueueCollectionTypeInterface::class);

        $mock1->expects($this->once())->method('create')->with([], [])->willReturn($queueCollection1);
        $mock2->expects($this->once())->method('create')->with(['a' => 1], [])->willReturn($queueCollection2);
        $mock3->expects($this->once())->method('create')->with([], ['b' => 2])->willReturn($queueCollection3);

        $this->s()->addQueueCollectionType('type1', $mock1);
        $this->s()->addQueueCollectionType('type2', $mock2);
        $this->s()->addQueueCollectionType('type3', $mock3);

        $this->assertEquals($queueCollection1, $this->s()->create('type1'));
        $this->assertEquals($queueCollection2, $this->s()->create('type2', ['a' => 1]));
        $this->assertEquals($queueCollection3, $this->s()->create('type3', [], ['b' => 2]));
    }
    /**
     * @group unit
     */
    public function testCreateForUnknownTypeThrowException()
    {
        $this->expectExceptionThrown(new RuntimeException("No 'type1' in queueCollectionTypes list", 412));
        $this->s()->create('type1');
    }
}
