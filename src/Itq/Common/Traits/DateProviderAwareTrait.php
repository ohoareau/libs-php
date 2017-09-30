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

use Itq\Common\DateProviderInterface;

/**
 * DateProviderAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DateProviderAwareTrait
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
     * @param DateProviderInterface $service
     *
     * @return $this
     */
    public function setDateProvider(DateProviderInterface $service)
    {
        return $this->setService('dateProvider', $service);
    }
    /**
     * @return DateProviderInterface
     */
    public function getDateProvider()
    {
        return $this->getService('dateProvider');
    }
}
