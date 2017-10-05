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

use Itq\Common\Service\ContextService;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ContextServiceAwareTrait
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
     * @return ContextService
     */
    public function getContextService()
    {
        return $this->getService('contextService');
    }
    /**
     * @param ContextService $service
     *
     * @return $this
     */
    public function setContextService(ContextService $service)
    {
        return $this->setService('contextService', $service);
    }
}
