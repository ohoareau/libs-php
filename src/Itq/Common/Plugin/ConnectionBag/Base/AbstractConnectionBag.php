<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ConnectionBag\Base;

use Itq\Common\ConnectionInterface;
use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\ConnectionBagInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractConnectionBag extends AbstractPlugin implements ConnectionBagInterface
{
    /**
     * @param array $connections
     */
    public function __construct(array $connections = [])
    {
        $this->initConnections($connections);
    }
    /**
     * @param array $params
     * @param array $options
     *
     * @return ConnectionInterface
     */
    public function getConnection(array $params = [], array $options = [])
    {
        return $this->selectConnection($params, $options);
    }
    /**
     * @param array $connections
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function initConnections(array $connections)
    {
        foreach ($connections as $name => $connection) {
            $matches = null;
            if (0 < preg_match('/\{([^\}]+)\}/', $name, $matches)) {
                $this->addConnectionTemplate($matches[1], $name, $connection);
                continue;
            }
            if (! ($connection instanceof ConnectionInterface)) {
                $connection = $this->createConnection($connection);
            }
            $this->addConnection($name, $connection);
        }

        return $this;
    }
    /**
     * @param array $connection
     *
     * @return ConnectionInterface
     *
     * @throws \Exception
     */
    abstract protected function createConnection(array $connection);
    /**
     * @param string              $name
     * @param ConnectionInterface $connection
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function addConnection($name, ConnectionInterface $connection)
    {
        return $this->setArrayParameterKey('connections', $name, $connection);
    }
    /**
     * @param string $variable
     * @param string $namePattern
     * @param array  $connectionInfos
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function addConnectionTemplate($variable, $namePattern, $connectionInfos)
    {
        $this->setArrayParameterKey('variables', $variable, true);

        return $this->setArrayParameterKey('connectionTemplates', $variable, ['namePattern' => $namePattern, 'connectionInfos' => $connectionInfos]);
    }
    /**
     * @param array $params
     * @param array $options
     *
     * @return ConnectionInterface
     *
     * @throws \Exception
     */
    protected function selectConnection(array $params, array $options = [])
    {
        $connectionNames = [];

        if (isset($params['connection'])) {
            $connectionNames[] = $params['connection'];
        }

        if (isset($params['operation'])) {
            $connectionNames[] = $params['operation'];
        }

        if (isset($params['operationType'])) {
            $connectionNames[] = $params['operationType'];
        }

        $connectionNames[] = 'default';

        $variables = $this->getArrayParameter('variables');

        foreach (array_keys($variables) as $variable) {
            if (isset($params[$variable])) {
                $nn = $this->getArrayParameterKey('connectionTemplates', $variable);
                $name = $this->replaceParams($nn['namePattern'], [$variable => $params[$variable]]);
                foreach ($connectionNames as $k => $v) {
                    $connectionNames[$k] = $this->replaceParams($v, [$variable => $params[$variable]]);
                }
                if (!$this->hasArrayParameterKey('connections', $name)) {
                    $this->addConnection($name, $this->createConnection($this->replaceParams($nn['connectionInfos'], $this->replaceParams($params, $params))));
                }
            }
        }

        foreach ($connectionNames as $connectionName) {
            if ($this->hasArrayParameterKey('connections', $connectionName)) {
                return $this->getArrayParameterKey('connections', $connectionName);
            }
        }

        unset($options);

        throw $this->createUnexpectedException('No connection available');
    }
    /**
     * @param mixed $data
     * @param array $params
     *
     * @return array|string
     */
    protected function replaceParams($data, $params)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$this->replaceParams($k, $params)] = $this->replaceParams($v, $params);
            }

            return $data;
        }

        if (is_object($data)) {
            return $data;
        }

        if (0 < preg_match_all('/\{([^\}]+)\}/', $data, $matches)) {
            foreach ($matches[1] as $i => $match) {
                $data = str_replace($matches[0][$i], isset($params[$match]) ? $params[$match] : null, $data);
            }
        }

        return $data;
    }
}
