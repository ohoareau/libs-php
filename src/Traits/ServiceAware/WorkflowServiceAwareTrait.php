<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\WorkflowService;

/**
 * WorkflowServiceAware trait.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
trait WorkflowServiceAwareTrait
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
     * @return WorkflowService
     */
    public function getWorkflowService()
    {
        return $this->getService('workflow');
    }
    /**
     * @param WorkflowService $service
     *
     * @return $this
     */
    public function setWorkflowService(WorkflowService $service)
    {
        return $this->setService('workflow', $service);
    }
}
