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

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * TokenStorageAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait TokenStorageAwareTrait
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
     * @param TokenStorageInterface $tokenStorageInterface
     *
     * @return $this
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorageInterface)
    {
        return $this->setService('tokenStorage', $tokenStorageInterface);
    }
    /**
     * @return TokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->getService('tokenStorage');
    }
}
