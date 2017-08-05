<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\DocumentBuilderService;

/**
 * DocumentBuilderServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DocumentBuilderServiceAwareTrait
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
     * @return DocumentBuilderService
     */
    public function getDocumentBuilderService()
    {
        return $this->getService('documentBuilder');
    }
    /**
     * @param DocumentBuilderService $service
     *
     * @return $this
     */
    public function setDocumentBuilderService(DocumentBuilderService $service)
    {
        return $this->setService('documentBuilder', $service);
    }
}
