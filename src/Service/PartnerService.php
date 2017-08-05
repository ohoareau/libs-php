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

/**
 * Partner Service.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
class PartnerService
{
    use Traits\ServiceTrait;
    /**
     * Register a partner of the specified type for the specified id (replace if exist).
     *
     * @param string $type
     * @param string $id
     * @param mixed  $service
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function register($type, $id, $service, array $options = [])
    {
        $this->checkTypeExist($type);

        $typeDefinition = $this->getArrayParameterListKey('types', $type);

        if (isset($typeDefinition['interface'])) {
            $typeInterface = $typeDefinition['interface'];

            if (!($service instanceof $typeInterface)) {
                throw $this->createRequiredException("Partner '%s' must implement interface '%s' to be compatible with partner type '%s' (found class: %s)", $id, $typeInterface, $type, get_class($service));
            }
        }

        return $this->setArrayParameterKey('types_'.$type.'s', $id, ['service' => $service] + $options);
    }
    /**
     * @param string $name
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkTypeExist($name)
    {
        if (!$this->hasType($name)) {
            throw $this->createRequiredException("No partner type '%s' declared", $name);
        }

        return $this;
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasType($name)
    {
        return $this->hasArrayParameterKey('types', $name);
    }
    /**
     * Register a partner type specified name (replace if exist).
     *
     * @param string $name
     * @param array  $definition
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function registerType($name, array $definition = [], array $options = [])
    {
        if (isset($definition['interface']) && !interface_exists($definition['interface'])) {
            throw $this->createRequiredException("Interface '%s' is required (but missing) for registration of partner type '%s'", $name);
        }

        return $this->setArrayParameterKey('types', $name, $definition + $options);
    }
    /**
     * Return the partner registered for the specified name.
     *
     * @param string $type
     * @param string $id
     *
     * @return array
     *
     * @throws \Exception if no partner registered for this name
     */
    public function getByType($type, $id)
    {
        $this->checkTypeExist($type);

        if (!$this->hasArrayParameterKey('types_'.$type.'s', $id)) {
            throw $this->createRequiredException("No partner '%s' in %s list", $id, $type);
        }

        return $this->getArrayParameterListKey('types_'.$type.'s', $id);
    }
    /**
     * @param string $type
     * @param string $id
     * @param string $operation
     * @param array  $params
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function executeOperation($type, $id, $operation, $params = [], $options = [])
    {
        $partnerInfo = $this->getByType($type, $id);

        $service = $partnerInfo['service'];
        unset($partnerInfo['service']);

        if (!method_exists($service, $operation)) {
            throw $this->createRequiredException("Operation '%s' is not provided by %s partner '%s'", $operation, $type, $id);
        }

        return call_user_func_array([$service, $operation], [$params, $options]);
    }
}
