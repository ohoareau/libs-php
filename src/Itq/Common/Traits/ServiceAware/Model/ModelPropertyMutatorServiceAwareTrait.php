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

use Itq\Common\Service\Model\ModelPropertyMutatorServiceInterface;

/**
 * ModelPropertyMutatorServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelPropertyMutatorServiceAwareTrait
{
    /**
     * @return ModelPropertyMutatorServiceInterface
     */
    public function getModelPropertyMutatorService()
    {
        return $this->getService('modelPropertyMutatorService');
    }
    /**
     * @param ModelPropertyMutatorServiceInterface $service
     *
     * @return $this
     */
    public function setModelPropertyMutatorService(ModelPropertyMutatorServiceInterface $service)
    {
        return $this->setService('modelPropertyMutatorService', $service);
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
