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
use Itq\Common\Service\PollerService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/poller
 */
class PollerServiceTest extends AbstractServiceTestCase
{
    /**
     * @return PollerService
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
            ['pollerType', Plugin\PollerTypeInterface::class, ['create'], 'getPollerTypes', 'addPollerType', 'thetype', 'getPollerType'],
        ];
    }
    /**
     * @group unit
     */
    public function testCreate()
    {
        $poller1 = new Plugin\Poller\MemoryPoller();
        $poller2 = new Plugin\Poller\MemoryPoller();
        $poller3 = new Plugin\Poller\MemoryPoller();

        $mock1 = $this->mocked('pollerType1', Plugin\PollerTypeInterface::class);
        $mock2 = $this->mocked('pollerType2', Plugin\PollerTypeInterface::class);
        $mock3 = $this->mocked('pollerType3', Plugin\PollerTypeInterface::class);

        $mock1->expects($this->once())->method('create')->with([], [])->willReturn($poller1);
        $mock2->expects($this->once())->method('create')->with(['a' => 1], [])->willReturn($poller2);
        $mock3->expects($this->once())->method('create')->with([], ['b' => 2])->willReturn($poller3);

        $this->s()->addPollerType('type1', $mock1);
        $this->s()->addPollerType('type2', $mock2);
        $this->s()->addPollerType('type3', $mock3);

        $this->assertEquals($poller1, $this->s()->create('type1'));
        $this->assertEquals($poller2, $this->s()->create('type2', ['a' => 1]));
        $this->assertEquals($poller3, $this->s()->create('type3', [], ['b' => 2]));
    }
}
