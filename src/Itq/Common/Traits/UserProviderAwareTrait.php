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

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * User Provider Aware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait UserProviderAwareTrait
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
     * @param UserProviderInterface $service
     *
     * @return $this
     */
    public function setUserProvider(UserProviderInterface $service)
    {
        return $this->setService('userProvider', $service);
    }
    /**
     * @return UserProviderInterface
     */
    public function getUserProvider()
    {
        return $this->getService('userProvider');
    }
}
