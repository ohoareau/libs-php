<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Plugin\ConnectionBagInterface;

/**
 * Connection Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ConnectionService
{
    use Traits\ServiceTrait;
    /**
     * Register a connection bag for the specified type (replace if exist).
     *
     * @param string                 $type
     * @param ConnectionBagInterface $connectionBag
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($type, ConnectionBagInterface $connectionBag)
    {
        return $this->setArrayParameterKey('connectionBags', $type, $connectionBag);
    }
    /**
     * Return the connection bag registered for the specified type.
     *
     * @param string $type
     *
     * @return ConnectionBagInterface
     *
     * @throws \Exception if no connection bag registered for this type
     */
    public function get($type)
    {
        return $this->getArrayParameterKey('connectionBags', $type);
    }
    /**
     * @param string $type
     * @param array  $params
     * @param array  $options
     *
     * @return mixed
     */
    public function getConnection($type, $params = [], $options = [])
    {
        return $this->get($type)->getConnection($params, $options);
    }
}
