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

use Itq\Common\Service\TemplateService;

/**
 * TemplateServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait TemplateServiceAwareTrait
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
     * @return TemplateService
     */
    public function getTemplateService()
    {
        return $this->getService('template');
    }
    /**
     * @param TemplateService $service
     *
     * @return $this
     */
    public function setTemplateService(TemplateService $service)
    {
        return $this->setService('template', $service);
    }
}
