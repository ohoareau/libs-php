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

use Itq\Common\Service\ConverterService;

/**
 * ConverterServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ConverterServiceAwareTrait
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
     * @return ConverterService
     */
    public function getConverterService()
    {
        return $this->getService('converter');
    }
    /**
     * @param ConverterService $service
     *
     * @return $this
     */
    public function setConverterService(ConverterService $service)
    {
        return $this->setService('converter', $service);
    }
}
