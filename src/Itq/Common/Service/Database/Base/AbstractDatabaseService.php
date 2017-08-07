<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Database\Base;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ConnectionInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractDatabaseService implements Service\DatabaseServiceInterface
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\ConnectionServiceAwareTrait;
    /**
     * @var \DateTime[]
     */
    protected $timers;
    /**
     * @param Service\ConnectionService $connectionService
     * @param EventDispatcherInterface  $eventDispatcher
     *
     * @throws \Exception
     */
    public function __construct(Service\ConnectionService $connectionService, EventDispatcherInterface $eventDispatcher)
    {
        $this->timers = [];

        $this->setConnectionService($connectionService);
        $this->setEventDispatcher($eventDispatcher);
    }
    /**
     * @param string $partition
     * @param string $name
     * @param array  $options
     *
     * @return void
     *
     * @throws \Exception
     */
    public function dropIndex($partition, $name, $options = [])
    {
        throw $this->createNotYetImplementedException('dropIndex');
    }
    /**
     * @return string
     */
    abstract public function getDatabaseType();
    /**
     * @param array $params
     * @param array $options
     *
     * @return ConnectionInterface
     */
    protected function getConnection($params = [], $options = [])
    {
        return $this->getConnectionService()->getConnection($this->getDatabaseType(), $params, $options);
    }
    /**
     * @return float
     */
    protected function start()
    {
        $now = microtime(true);

        $this->timers[] = $now;

        return $now;
    }
    /**
     * @return float[]
     *
     * @throws \Exception
     */
    protected function stop()
    {
        if (!count($this->timers)) {
            $this->start();
        }

        $endDate = microtime(true);

        return [array_pop($this->timers), $endDate];
    }
}
