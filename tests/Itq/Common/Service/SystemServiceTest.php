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

use Itq\Common\Service\SystemService;
use Itq\Common\Adapter\SystemAdapterInterface;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/system
 */
class SystemServiceTest extends AbstractServiceTestCase
{
    /**
     * @return SystemService
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
        return [$this->mocked('systemAdapter', SystemAdapterInterface::class)];
    }
    /**
     * @group unit
     */
    public function testSetCurrentTime()
    {
        $this->assertEquals(0, $this->s()->getCurrentTimeOffset());
        $this->assertEquals($this->s(), $this->s()->setCurrentTime(12));
        $this->assertNotEquals(0, $this->s()->getCurrentTimeOffset());
    }
    /**
     * @param int $expectedTime
     * @param int $expectedOffset
     * @param int $systemTime
     * @param int $forcedCurrentTime
     *
     * @group unit
     *
     * @dataProvider getGetCurrentTimeData
     */
    public function testGetCurrentTime($expectedTime, $expectedOffset, $systemTime, $forcedCurrentTime)
    {
        $this->mocked('systemAdapter')->expects($this->any())->method('microtime')->willReturn($systemTime);
        $this->s()->setCurrentTime($forcedCurrentTime);

        $this->assertEquals($expectedTime, $this->s()->getCurrentTime());
        $this->assertEquals($expectedOffset, $this->s()->getCurrentTimeOffset());
    }
    /**
     * @return array
     */
    public function getGetCurrentTimeData()
    {
        return [
            [10, 5, 5, 10],
            [100, 20, 80, 100],
        ];
    }
}
