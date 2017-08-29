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

use Itq\Common\Service\CollectionService;

/**
 * CollectionServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CollectionServiceAwareTrait
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
     * @return CollectionService
     */
    public function getCollectionService()
    {
        return $this->getService('collection');
    }
    /**
     * @param CollectionService $service
     *
     * @return $this
     */
    public function setCollectionService(CollectionService $service)
    {
        return $this->setService('collection', $service);
    }
}
