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

use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group  services
 * @group  services/batch
 */
class BatchServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\BatchService
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
        return [$this->mockedEventDispatcher()];
    }

    /**
     * @group unit
     */
    public function testExecuteWithUnknownBatchThrowRuntimeException()
    {
        $this->expectExceptionThrown(new \RuntimeException("Unknown batch 'unknownBatch'", 404));
        $this->mockedEventDispatcher()->expects($this->once())->method('hasListeners')->will($this->returnValue(false));
        $this->s()->execute('unknownBatch');
    }

    /**
     * @group unit
     */
    public function testExecute()
    {
        $params = ['some params'];
        $options = ['opt1' => 'val1'];
        $expectedParams = $params + ['options' => $options];

        $this->mockedEventDispatcher()
            ->expects($this->once())
            ->method('hasListeners')
            ->with('batchs.amaaziiingBatch')
            ->will($this->returnValue(true));

        $this->mockedEventDispatcher()
            ->expects($this->once())
            ->method('dispatch')
            ->with('batchs.amaaziiingBatch')
            ->will($this->returnValue('dispatched'));

        $this->assertInstanceOf(Service\BatchService::class, $this->s()->execute('amaaziiingBatch', $params, $options));
    }

}
