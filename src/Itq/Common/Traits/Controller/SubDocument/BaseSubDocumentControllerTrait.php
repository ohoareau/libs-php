<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Controller\SubDocument;

use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait BaseSubDocumentControllerTrait
{
    /**
     * @param string $id
     *
     * @return object
     */
    abstract public function get($id);
    /**
     * Returns the implicit document service (based on class name)
     *
     * @param string $id
     *
     * @return Service\SubDocumentServiceInterface|object
     */
    protected function getService($id = null)
    {
        if (null === $id) {
            $id = preg_replace('/controller$/', '', join('.', array_slice(explode('.', str_replace('\\', '.', strtolower(get_class($this)))), -2)));
        }

        return $this->get('app.'.$id);
    }
}
