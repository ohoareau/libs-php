<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class CrudService
{
    use Traits\ServiceTrait;
    /**
     * @param string $name
     * @param mixed  $service
     *
     * @return $this
     */
    public function add($name, $service)
    {
        return $this->setArrayParameterKey('crudServices', strtolower($name), $service);
    }
    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function get($name)
    {
        return $this->getArrayParameterKey('crudServices', strtolower($name));
    }
    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getAll()
    {
        return $this->getArrayParameter('crudServices');
    }
    /**
     * @param string $name
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function has($name)
    {
        return $this->hasArrayParameterKey('crudServices', strtolower($name));
    }
}
