<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Controller;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Request Stack Aware Controller trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait RequestStackAwareControllerTrait
{
    /**
     * @param string $id
     *
     * @return object
     */
    abstract public function get($id);
    /**
     * @return RequestStack
     */
    public function getRequestStack()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get('request_stack');
    }
}
