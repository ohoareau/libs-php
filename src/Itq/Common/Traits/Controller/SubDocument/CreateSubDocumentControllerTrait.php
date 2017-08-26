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
trait CreateSubDocumentControllerTrait
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
    abstract protected function returnCreateResponse(Request $request, $data = null, array $options = []);
    /**
     * @param mixed   $data
     * @param int     $code
     * @param array   $headers
     * @param array   $options
     * @param Request $request
     *
     * @return Response
     */
    abstract protected function returnResponse($data = null, $code = 200, $headers = [], $options = [], Request $request = null);
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function handlePurge(Request $request, $options = []);
    /**
     * Create a new document.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handleCreate(Request $request, $options = [])
    {
        return $this->returnCreateResponse(
            $request,
            $this->getService()->create(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            $options
        );
    }
    /**
     * Create a new document.
     *
     * @param Request $request
     * @param string  $field
     * @param array   $options
     *
     * @return Response
     */
    protected function handleCreateBy(Request $request, $field, $options = [])
    {
        return $this->returnCreateResponse(
            $request,
            $this->getService()->createBy(
                $field,
                $this->getRequestService()->fetchRouteParameter($request, $field),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            $options
        );
    }

    /**
     * @param Request $request
     * @param array $options
     * @return Response
     */
    protected function handlePurgeAndCreate(Request $request, $options = [])
    {
        $this->handlePurge($request);
        $data = $this->getRequestService()->fetchRequestData($request);

        if (empty($data)) {
            return $this->returnResponse();
        }

        return $this->returnResponse(
            $this->getService()->createBulk(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            )
        );
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleCreateByToken(Request $request)
    {
        return $this->handleCreateBy($request, 'token');
    }
}
