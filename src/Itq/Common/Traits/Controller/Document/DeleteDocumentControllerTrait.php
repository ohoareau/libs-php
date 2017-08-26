<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Controller\Document;

use Exception;
use Itq\Common\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait DeleteDocumentControllerTrait
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
    abstract public function createNotFoundException($message = 'Not Found', Exception $previous = null);
    /**
     * @param string $id
     *
     * @return Service\DocumentServiceInterface|object
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
            $this->getRequestService()->fetchRouteParameter($request, 'id'),
            $options
        );

        return $this->returnDeleteResponse($request);
    }
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function returnPurgeResponse(Request $request, array $options = []);
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
        $this->getService()->purge($options);

        return $this->returnPurgeResponse($request);
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
        return $this->handleClearProperties($request, $property, $options);
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
            $this->getRequestService()->fetchRouteParameter($request, 'id'),
            $properties,
            ['validation_groups' => 'delete'] + $options
        );

        return $this->returnDeleteResponse($request, $options);
    }
    /**
     * @param Request $request
     * @param string  $property
     * @param string  $firstBy
     * @param string  $secondBy
     * @param array   $options
     *
     * @return Response
     *
     * @throws Exception
     */
    protected function handleClearPropertyByAndBy(Request $request, $property, $firstBy, $secondBy, array $options = [])
    {
        $firstByValue = $this->getRequestService()->fetchRouteParameter($request, $firstBy);
        $doc          = $this->getService()->findOne(
            [$firstBy => $firstByValue, $secondBy => $this->getRequestService()->fetchRouteParameter($request, $secondBy)],
            array_merge(['id' => true], $this->getRequestService()->fetchQueryFields($request))
        );

        if (null === $doc) {
            throw $this->createNotFoundException();
        }

        $this->getService()->update($doc->id, [$property => '*cleared*'], ['validation_groups' => 'delete'] + $options);

        return $this->returnDeleteResponse($request);
    }
    /**
     * @param Request $request
     * @param string  $firstBy
     * @param string  $secondBy
     * @param array   $options
     *
     * @return Response
     *
     * @throws Exception
     */
    protected function handleDeleteByAndBy(Request $request, $firstBy, $secondBy, array $options = [])
    {
        $doc = $this->getService()->findOne(
            [
                $firstBy  => $this->getRequestService()->fetchRouteParameter($request, $firstBy),
                $secondBy => $this->getRequestService()->fetchRouteParameter($request, $secondBy),
            ],
            array_merge(['id' => true], $this->getRequestService()->fetchQueryFields($request))
        );

        if (null === $doc) {
            throw $this->createNotFoundException();
        }

        $this->getService()->delete($doc->id, $options);

        return $this->returnDeleteResponse($request);
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleDeleteByTokenAndAdminToken(Request $request)
    {
        return $this->handleDeleteByAndBy($request, 'token', 'adminToken');
    }
    /**
     * @param Request $request
     * @param string  $property
     *
     * @return Response
     */
    protected function handleClearPropertyByTokenAndAdminToken(Request $request, $property)
    {
        return $this->handleClearPropertyByAndBy($request, $property, 'token', 'adminToken');
    }
}
