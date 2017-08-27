<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware\Model;

use Itq\Common\Service\Model\ModelObjectPopulatorServiceInterface;

/**
 * ModelObjectPopulatorServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelObjectPopulatorServiceAwareTrait
{
    /**
     * @return ModelObjectPopulatorServiceInterface
     */
    public function getModelObjectPopulatorService()
    {
        return $this->getService('modelObjectPopulatorService');
    }
    /**
     * @param ModelObjectPopulatorServiceInterface $service
     *
     * @return $this
     */
    public function setModelObjectPopulatorService(ModelObjectPopulatorServiceInterface $service)
    {
        return $this->setService('modelObjectPopulatorService', $service);
    }
    /**
     * @param string $key
     * @param mixed  $service
     *
     * @return $this
     */
    protected abstract function setService($key, $service);
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getService($key);
}
