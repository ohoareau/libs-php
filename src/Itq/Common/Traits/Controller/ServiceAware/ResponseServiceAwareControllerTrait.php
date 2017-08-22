<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Controller\ServiceAware;

use Itq\Common\Service;

/**
 * Response Service Aware Controller trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ResponseServiceAwareControllerTrait
{
    /**
     * @param string $id
     *
     * @return object
     */
    abstract public function get($id);
    /**
     * @return Service\ResponseService
     */
    public function getResponseService()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get('app.response');
    }
}
