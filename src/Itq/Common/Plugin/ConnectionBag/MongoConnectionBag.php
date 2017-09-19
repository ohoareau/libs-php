<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ConnectionBag;

use MongoClient;
use Itq\Common\ConnectionInterface;
use Itq\Common\Connection;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MongoConnectionBag extends Base\AbstractConnectionBag
{
    /**
     * @param ConnectionInterface $connection
     * @param string              $instanceId
     * @param array               $options
     *
     * @return string|null
     */
    protected function changeConnectionInstance(ConnectionInterface $connection, $instanceId, array $options = [])
    {
        $old = $connection->getParameter('db');

        $connection->setParameter('db', $instanceId);

        return $old;
    }
    /**
     * @param array $connection
     *
     * @return Connection
     *
     * @throws \Exception
     */
    protected function createConnection(array $connection)
    {
        $connection += ['server' => 'mongodb://localhost:27017', 'db' => null, 'random' => false];

        if (true === $connection['random']) {
            $connection['db'] .= '_'.((int) microtime(true)).'_'.substr(md5(rand(0, 10000)), -8);
        }

        if (64 <= $this->getStringLength($connection['db'])) {
            throw $this->createMalformedException(
                "Database name is too long, maximum is 64 characters (found: %d)",
                $this->getStringLength($connection['db'])
            );
        }

        return new Connection(new MongoClient($connection['server']), $connection);
    }
}
