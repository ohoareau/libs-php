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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait GetSubDocumentControllerTrait
{
    /**
     * @return Service\RequestService
     */
    abstract public function getRequestService();
    /**
     * @param string $id
     *
     * @return Service\SubDocumentServiceInterface|object
     */
    abstract protected function getService($id = null);
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function returnGetResponse(Request $request, $data = null, array $options = []);
    /**
     * Retrieve the specified document.
     *
     * @param Request $request
     * @param string  $parentFieldName
     * @param array   $options
     *
     * @return Response
     */
    protected function handleGetByParentField(Request $request, $parentFieldName, $options = [])
    {
        return $this->returnGetResponse(
            $request,
            $this->getService()->get(
                $this->getService()->getParentIdBy($parentFieldName, $this->getRequestService()->fetchRouteParameter($request, $parentFieldName)),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchQueryFields($request),
                $options
            ),
            $options
        );
    }
    /**
     * Retrieve the specified document.
     *
     * @param Request $request
     * @param string  $parentFieldName
     * @param string  $property
     * @param array   $options
     *
     * @return Response
     */
    protected function handleGetPropertyByParentField(Request $request, $parentFieldName, $property, $options = [])
    {
        return $this->returnGetResponse(
            $request,
            $this->getService()->getProperty(
                $this->getService()->getParentIdBy($parentFieldName, $this->getRequestService()->fetchRouteParameter($request, $parentFieldName)),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $property,
                $options
            ),
            $options
        );
    }
    /**
     * Return the specified document.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handleGet(Request $request, $options = [])
    {
        return $this->returnGetResponse(
            $request,
            $this->getService()->get(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchQueryFields($request),
                $options
            ),
            $options
        );
    }
}
