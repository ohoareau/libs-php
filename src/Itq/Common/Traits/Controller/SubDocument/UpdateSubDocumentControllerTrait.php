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

use Exception;
use Itq\Common\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait UpdateSubDocumentControllerTrait
{
    /**
     * @return Service\RequestService
     */
    abstract public function getRequestService();
    /**
     * @param string         $message
     * @param Exception|null $previous
     *
     * @return Exception
     */
    abstract public function createAccessDeniedException($message = 'Access Denied.', Exception $previous = null);
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
    abstract protected function returnUpdateResponse(Request $request, $data = null, array $options = []);
    /**
     * Update the specified document.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handleUpdate(Request $request, $options = [])
    {
        return $this->returnUpdateResponse(
            $request,
            $this->getService()->update(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            $options
        );
    }
    /**
     * Update the specified document.
     *
     * @param Request $request
     * @param string  $parentFieldName
     * @param array   $options
     *
     * @return Response
     */
    protected function handleUpdateByParentField(Request $request, $parentFieldName, $options = [])
    {
        return $this->returnUpdateResponse(
            $request,
            $this->getService()->update(
                $this->getService()->getParentIdBy($parentFieldName, $this->getRequestService()->fetchRouteParameter($request, $parentFieldName)),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            $options
        );
    }
    /**
     * Update the specified document property.
     *
     * @param Request $request
     * @param string  $property
     * @param array   $options
     *
     * @return Response
     *
     * @throws Exception
     */
    protected function handleUpdateProperty(Request $request, $property, $options = [])
    {
        $service = $this->getService();
        $method  = 'update'.ucfirst($property);

        if (!method_exists($service, $method)) {
            throw $this->createAccessDeniedException();
        }

        return $this->returnUpdateResponse(
            $request,
            $service->$method(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            $options
        );
    }
}
