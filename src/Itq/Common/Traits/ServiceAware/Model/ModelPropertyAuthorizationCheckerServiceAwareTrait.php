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

use Itq\Common\Service\Model\ModelPropertyAuthorizationCheckerServiceInterface;

/**
 * ModelPropertyAuthorizationCheckerServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelPropertyAuthorizationCheckerServiceAwareTrait
{
    /**
     * @return ModelPropertyAuthorizationCheckerServiceInterface
     */
    public function getModelPropertyAuthorizationCheckerService()
    {
        return $this->getService('modelPropertyAuthorizationCheckerService');
    }
    /**
     * @param ModelPropertyAuthorizationCheckerServiceInterface $service
     *
     * @return $this
     */
    public function setModelPropertyAuthorizationCheckerService(ModelPropertyAuthorizationCheckerServiceInterface $service)
    {
        return $this->setService('modelPropertyAuthorizationCheckerService', $service);
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
