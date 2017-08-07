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

use JMS\Serializer\SerializerInterface;

/**
 * SerializerAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SerializerAwareTrait
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
     * @param SerializerInterface $service
     *
     * @return $this
     */
    public function setSerializer(SerializerInterface $service)
    {
        return $this->setService('serializer', $service);
    }
    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->getService('serializer');
    }
}
