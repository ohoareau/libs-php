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

use Itq\Common\Plugin;
use Itq\Common\Service\PollableSourceService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/pollable-source
 */
class PollableSourceServiceTest extends AbstractServiceTestCase
{
    /**
     * @return PollableSourceService
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
            ['pollableSourceType', Plugin\PollableSourceTypeInterface::class, ['create'], 'getPollableSourceTypes', 'addPollableSourceType', 'thetype', 'getPollableSourceType'],
        ];
    }
    /**
     * @group unit
     */
    public function testCreate()
    {
        $pollableSource1 = new Plugin\PollableSource\MemoryPollableSource();
        $pollableSource2 = new Plugin\PollableSource\MemoryPollableSource();
        $pollableSource3 = new Plugin\PollableSource\MemoryPollableSource();

        $mock1 = $this->mocked('pollableSourceType1', Plugin\PollableSourceTypeInterface::class);
        $mock2 = $this->mocked('pollableSourceType2', Plugin\PollableSourceTypeInterface::class);
        $mock3 = $this->mocked('pollableSourceType3', Plugin\PollableSourceTypeInterface::class);

        $mock1->expects($this->once())->method('create')->with([], [])->willReturn($pollableSource1);
        $mock2->expects($this->once())->method('create')->with(['a' => 1], [])->willReturn($pollableSource2);
        $mock3->expects($this->once())->method('create')->with([], ['b' => 2])->willReturn($pollableSource3);

        $this->s()->addPollableSourceType('type1', $mock1);
        $this->s()->addPollableSourceType('type2', $mock2);
        $this->s()->addPollableSourceType('type3', $mock3);

        $this->assertEquals($pollableSource1, $this->s()->create('type1'));
        $this->assertEquals($pollableSource2, $this->s()->create('type2', ['a' => 1]));
        $this->assertEquals($pollableSource3, $this->s()->create('type3', [], ['b' => 2]));
    }
}
