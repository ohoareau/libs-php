<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Microservice;

use Itq\Common\Service\PollerService;
use Itq\Common\Service\PollableSourceService;
use Itq\Common\Service\QueueCollectionService;
use Itq\Common\Plugin\PollerType\MemoryPollerType;
use Itq\Common\Plugin\Microservice\DispatcherMicroservice;
use Itq\Common\Plugin\PollableSourceType\MemoryPollableSourceType;
use Itq\Common\Plugin\QueueCollectionType\MemoryQueueCollectionType;
use Itq\Common\Tests\Plugin\Microservice\Base\AbstractMicroserviceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/microservices
 * @group plugins/microservices/dispatcher
 */
class DispatcherMicroserviceTest extends AbstractMicroserviceTestCase
{
    /**
     * @return DispatcherMicroservice
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
        return [
            $this->mockedPollerService(),
            $this->mockedPollableSourceService(),
            $this->mockedQueueCollectionService(),
            'memory',
            [],
            [],
        ];
    }
    /**
     * @group integ
     */
    public function testStart()
    {
        $qcs = new QueueCollectionService();
        $qcs->addQueueCollectionType('memory', new MemoryQueueCollectionType());
        $pss = new PollableSourceService();
        $pss->addPollableSourceType('memory', new MemoryPollableSourceType());
        $ps = new PollerService();
        $ps->addPollerType('memory', new MemoryPollerType());
        $this->m()->setPollableSourceService($pss);
        $this->m()->setQueueCollectionService($qcs);
        $this->m()->setOptions([]);
        $this->m()->setSourceDefinitions([]);
        $this->m()->setPollerService($ps);
        $this->m()->setIdleCallback(
            function ($ctx) {
                $ctx->running = false;
            }
        );
        $this->m()->start();
    }
}
