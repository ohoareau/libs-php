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

use Itq\Common\Service\MathService;

/**
 * MathServiceAware trait.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
trait MathServiceAwareTrait
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
     * @return MathService
     */
    public function getMathService()
    {
        return $this->getService('math');
    }
    /**
     * @param MathService $service
     *
     * @return $this
     */
    public function setMathService(MathService $service)
    {
        return $this->setService('math', $service);
    }
}
