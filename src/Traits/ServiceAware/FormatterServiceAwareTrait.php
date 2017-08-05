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

use Itq\Common\Service\FormatterService;

/**
 * FormatterServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait FormatterServiceAwareTrait
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
     * @return FormatterService
     */
    public function getFormatterService()
    {
        return $this->getService('formatter');
    }
    /**
     * @param FormatterService $service
     *
     * @return $this
     */
    public function setFormatterService(FormatterService $service)
    {
        return $this->setService('formatter', $service);
    }
}
