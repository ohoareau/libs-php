<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\ModelServiceInterface;

/**
 * ModelServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ModelServiceAwareTrait
{
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
    /**
     * @param ModelServiceInterface $service
     *
     * @return $this
     */
    public function setModelService(ModelServiceInterface $service)
    {
        return $this->setService('model', $service);
    }
    /**
     * @return ModelServiceInterface
     */
    public function getModelService()
    {
        return $this->getService('model');
    }
}
