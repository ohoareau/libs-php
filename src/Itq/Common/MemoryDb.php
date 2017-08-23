<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryDb
{
    use Traits\BaseTrait;
    /**
     * @param string $name
     */
    public function dropDatabase($name)
    {
        $this->unsetParameter($this->dbName($name));
    }
    /**
     * @param string $db
     * @param string $name
     *
     * @return MemoryDbCollection
     */
    public function selectCollection($db, $name)
    {
        return $this->db($db)->collection($name);
    }
    /**
     * @param string $name
     *
     * @return string
     */
    protected function dbName($name)
    {
        return sprintf('db.%s', $name);
    }
    /**
     * @param string $name
     *
     * @return object
     *
     * @throws \Exception
     */
    protected function db($name)
    {
        $dbName = $this->dbName($name);

        if (!isset($this->parameters[$dbName])) {
            throw $this->createFailedException("Unknown database '%s'", $dbName);
        }

        return $this->parameters[$dbName];
    }
}
