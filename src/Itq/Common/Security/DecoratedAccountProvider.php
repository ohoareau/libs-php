<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Security;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DecoratedAccountProvider
{
    use Traits\ServiceTrait;
    /**
     * @param mixed  $accountProvider
     * @param string $method
     *
     * @throws \Exception
     */
    public function __construct($accountProvider, $method = 'get')
    {
        if (!method_exists($accountProvider, $method)) {
            throw $this->createUnexpectedException("Missing method %s::%s()", get_class($accountProvider), $method);
        }

        $this->setParameter('accountProvider', $accountProvider);
        $this->setParameter('method', $method);
    }
    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return $this->getParameter('accountProvider')->{$this->getParameter('method')}($id);
    }
}
