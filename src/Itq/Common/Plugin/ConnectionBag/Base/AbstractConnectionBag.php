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

use Exception;
use Itq\Common\Model;
use Itq\Common\Traits;
use Itq\Common\ConnectionInterface;
use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\ConnectionBagInterface;
use Itq\Common\Aware\InstanceChangeAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractConnectionBag extends AbstractPlugin implements ConnectionBagInterface, InstanceChangeAwareInterface
{
    use Traits\Helper\String\ReplaceVarsTrait;
    /**
     * @param array $connections
     */
    public function __construct(array $connections = [])
    {
        $this->initConnections($connections);
        $this->setInstanceId('default');
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
     * @param Model\Internal\Instance $instance
     * @param array                   $options
     *
     * @return void
     *
     * @throws Exception
     */
    public function changeInstance(Model\Internal\Instance $instance, array $options = [])
    {
        if ('default' !== $this->getInstanceId()) {
            $this->changeInstanceToDefault($options);
        }

        $connections = $this->getConnections();

        $backedUp = [];

        foreach ($connections as $i => $connection) {
            $backedUp[$i] = $this->changeConnectionInstance($connection, $instance->id, $options);
        }

        $this->setParameter('backedUpDataForConnections', $backedUp);

        $this->setInstanceId($instance->id);
    }
    /**
     * @param Model\Internal\Instance $instance
     * @param array                   $options
     *
     * @return void
     *
     * @throws Exception
     */
    public function cleanInstance(Model\Internal\Instance $instance, array $options = [])
    {
        if ('default' === $this->getInstanceId()) {
            throw $this->createDeniedException('default instance not cleanable');
        }

        $connections = $this->getConnections();

        foreach ($connections as $i => $connection) {
            $this->cleanConnectionInstance($connection, $instance->id, $options);
        }
    }
    /**
     * @param array $options
     *
     * @return void
     *
     * @throws Exception
     */
    public function changeInstanceToDefault(array $options = [])
    {
        $backedUp = $this->getArrayParameter('backedUpDataForConnections');

        foreach ($this->getConnections() as $i => $connection) {
            $this->changeConnectionInstance($connection, $backedUp[$i], $options);
        }

        $this->unsetParameter('backedUpDataForConnections');
        $this->setInstanceId('default');
    }
    /**
     * @param ConnectionInterface $connection
     * @param string              $instanceId
     * @param array               $options
     *
     * @return string|null
     */
    abstract protected function changeConnectionInstance(ConnectionInterface $connection, $instanceId, array $options = []);
    /**
     * @param ConnectionInterface $connection
     * @param string              $instanceId
     * @param array               $options
     *
     * @return void
     */
    abstract protected function cleanConnectionInstance(ConnectionInterface $connection, $instanceId, array $options = []);
    /**
     * @param string $id
     *
     * @return $this
     */
    protected function setInstanceId($id)
    {
        return $this->setParameter('instanceId', $id);
    }
    /**
     * @return string
     */
    protected function getInstanceId()
    {
        return $this->getParameter('instanceId');
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
            if (!($connection instanceof ConnectionInterface)) {
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
     * @return ConnectionInterface[]
     */
    protected function getConnections()
    {
        return $this->getArrayParameter('connections');
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
                $name = $this->replaceVars($nn['namePattern'], [$variable => $params[$variable]]);
                foreach ($connectionNames as $k => $v) {
                    $connectionNames[$k] = $this->replaceVars($v, [$variable => $params[$variable]]);
                }
                if (!$this->hasArrayParameterKey('connections', $name)) {
                    $this->addConnection($name, $this->createConnection($this->replaceVars($nn['connectionInfos'], $this->replaceVars($params, $params))));
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
}
