<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataProvider\Supervision\Identity;

use Itq\Common\Traits;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SymfonyTokenIdentitySupervisionDataProvider extends Base\AbstractIdentitySupervisionDataProvider
{
    use Traits\TokenStorageAwareTrait;
    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->setTokenStorage($tokenStorage);
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function provide(array $options = [])
    {
        return (array) $this->getTokenStorage()->getToken();
    }
}
