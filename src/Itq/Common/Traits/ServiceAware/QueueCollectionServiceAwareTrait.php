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

use Itq\Common\Service\QueueCollectionService;

/**
 * QueueCollectionServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait QueueCollectionServiceAwareTrait
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
     * @return QueueCollectionService
     */
    public function getQueueCollectionService()
    {
        return $this->getService('queueCollectionService');
    }
    /**
     * @param QueueCollectionService $service
     *
     * @return $this
     */
    public function setQueueCollectionService(QueueCollectionService $service)
    {
        return $this->setService('queueCollectionService', $service);
    }
}
