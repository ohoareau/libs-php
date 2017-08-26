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
trait DeleteSubDocumentControllerTrait
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
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function returnDeleteResponse(Request $request, array $options = []);
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function returnPurgeResponse(Request $request, array $options = []);
    /**
     * Delete the specified document.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handleDelete(Request $request, $options = [])
    {
        $this->getService()->delete(
            $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
            $this->getRequestService()->fetchRouteParameter($request, 'id'),
            $options
        );

        return $this->returnDeleteResponse($request, $options);
    }
    /**
     * Purge (delete) all the documents.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handlePurge(Request $request, $options = [])
    {
        $this->getService()->purge(
            $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
            $options
        );

        return $this->returnPurgeResponse($request, $options);
    }
    /**
     * Clear the specified document property.
     *
     * @param Request $request
     * @param string  $property
     * @param array   $options
     *
     * @return Response
     */
    protected function handleClearProperty(Request $request, $property, $options = [])
    {
        return $this->handleClearProperties($request, [$property], $options);
    }
    /**
     * Clear the specified document properties.
     *
     * @param Request      $request
     * @param string|array $properties
     * @param array        $options
     *
     * @return Response
     */
    protected function handleClearProperties(Request $request, $properties, $options = [])
    {
        if (!is_array($properties)) {
            $properties = explode(',', $properties);
        }

        foreach ($properties as $k => $v) {
            unset($properties[$k]);
            $properties[$v] = '*cleared*';
        }

        $this->getService()->update(
            $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
            $this->getRequestService()->fetchRouteParameter($request, 'id'),
            $properties,
            ['validation_groups' => 'delete'] + $options
        );

        return $this->returnDeleteResponse($request, $options);
    }
}
