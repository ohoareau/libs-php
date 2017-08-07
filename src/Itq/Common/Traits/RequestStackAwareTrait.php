<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * RequestStackAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait RequestStackAwareTrait
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
     * @param RequestStack $service
     *
     * @return $this
     */
    public function setRequestStack(RequestStack $service)
    {
        return $this->setService('requestStack', $service);
    }
    /**
     * @return RequestStack
     */
    public function getRequestStack()
    {
        return $this->getService('requestStack');
    }
}
