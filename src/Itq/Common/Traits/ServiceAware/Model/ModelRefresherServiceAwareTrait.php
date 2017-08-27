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

use Itq\Common\Service\Model\ModelRefresherServiceInterface;

/**
 * ModelRefresherServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelRefresherServiceAwareTrait
{
    /**
     * @return ModelRefresherServiceInterface
     */
    public function getModelRefresherService()
    {
        return $this->getService('modelRefresherService');
    }
    /**
     * @param ModelRefresherServiceInterface $service
     *
     * @return $this
     */
    public function setModelRefresherService(ModelRefresherServiceInterface $service)
    {
        return $this->setService('modelRefresherService', $service);
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
