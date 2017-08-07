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

use Itq\Common\Connection;
use Itq\Common\Plugin\ConnectionBag\Base\AbstractConnectionBag;

use MongoClient;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MongoConnectionBag extends AbstractConnectionBag
{
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

        if (64 <= strlen($connection['db'])) {
            throw $this->createMalformedException(
                "Database name is too long, maximum is 64 characters (found: %d)",
                strlen($connection['db'])
            );
        }

        return new Connection(new MongoClient($connection['server']), $connection);
    }
}
