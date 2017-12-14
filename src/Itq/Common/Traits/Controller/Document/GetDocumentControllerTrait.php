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
trait GetDocumentControllerTrait
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
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function returnGetResponse(Request $request, $data = null, array $options = []);
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
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchQueryFields($request),
                $options
            ),
            $options
        );
    }
    /**
     * Return the specified document by the specified field.
     *
     * @param Request $request
     * @param string  $field
     * @param array   $options
     *
     * @return Response
     */
    protected function handleGetBy(Request $request, $field, $options = [])
    {
        return $this->returnGetResponse($request, $this->executeGetBy($request, $field, $options), $options);
    }
    /**
     * Return the specified document property by the specified field.
     *
     * @param Request $request
     * @param string  $field
     * @param string  $property
     * @param array   $options
     *
     * @return Response
     */
    protected function handleGetPropertyBy(Request $request, $field, $property, $options = [])
    {
        return $this->returnGetResponse(
            $request,
            $this->getService()->getPropertyBy(
                $field,
                $this->getRequestService()->fetchRouteParameter($request, $field),
                $property,
                $options
            ),
            $options
        );
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleGetContentByToken(Request $request)
    {
        return $this->handleGetPropertyBy($request, 'token', 'content', ['formatProperty' => 'contentType']);
    }
    /**
     * Return the specified document property.
     *
     * @param Request $request
     * @param string  $property
     * @param array   $options
     *
     * @return Response
     */
    protected function handleGetProperty(Request $request, $property, $options = [])
    {
        $service = $this->getService();
        $method  = 'get'.ucfirst($property);
        $id      = $this->getRequestService()->fetchRouteParameter($request, 'id');

        return $this->returnGetResponse(
            $request,
            method_exists($service, $method) ? $service->$method($id, $options) : $this->getService()->getProperty($id, $property, $options),
            $options
        );
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
    protected function handleGetByAndBy(Request $request, $firstBy, $secondBy, array $options = [])
    {
        $doc = $this->executeGetBy($request, $firstBy, ['extraFields' => [$secondBy => true]] + $options);

        if ($this->getRequestService()->fetchRouteParameter($request, $secondBy) !== $doc->$secondBy) {
            throw $this->createNotFoundException();
        }

        return $this->returnGetResponse($request, $doc);
    }
    /**
     * @param Request     $request
     * @param string      $field
     * @param string      $otherField
     * @param string      $bagClass
     * @param null|string $otherService
     * @param array       $options
     *
     * @return Response
     *
     * @throws Exception
     */
    protected function handleGetDoubleBagBy(Request $request, $field, $otherField, $bagClass, $otherService = null, array $options = [])
    {
        if (null === $otherService) {
            $otherService = $otherField;
        }

        $doc = $this->executeGetBy($request, $field, ['extraFields' => ['id' => true, $otherField => true]] + $options);

        if (!$doc->$otherField) {
            throw $this->createNotFoundException(sprintf('Doc exist but no associated %s', $otherField));
        }

        return $this->returnGetResponse(
            $request,
            new $bagClass(
                $this->getService($otherService)->get(
                    $doc->$otherField,
                    $this->getRequestService()->fetchQueryFields($request, ['key' => sprintf('%sFields', $otherField)])
                ),
                $doc
            ),
            $options
        );
    }
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    protected function handleGetByTokenAndAdminToken(Request $request)
    {
        return $this->handleGetByAndBy($request, 'token', 'adminToken');
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleGetByCode(Request $request)
    {
        return $this->handleGetBy($request, 'code');
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleGetByToken(Request $request)
    {
        return $this->handleGetBy($request, 'token');
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleGetByLogin(Request $request)
    {
        return $this->handleGetBy($request, 'login');
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleGetByHash(Request $request)
    {
        return $this->handleGetBy($request, 'hash');
    }
    /**
     * @param Request $request
     * @param string  $field
     * @param array   $options
     *
     * @return mixed
     */
    protected function executeGetBy(Request $request, $field, $options = [])
    {
        $service    = $this->getService();
        $method     = sprintf('getBy%s', ucfirst($field));
        $fieldValue = $this->getRequestService()->fetchRouteParameter($request, $field);
        $fields     = $this->getRequestService()->fetchQueryFields($request);

        if (isset($options['extraFields']) && is_array($options['extraFields'])) {
            $fields = !empty($fields) ? (array_merge($fields, $options['extraFields'])) : [];
        }

        return true === method_exists($service, $method) ?
            $service->$method($fieldValue, $fields, $options) :
            $service->getBy($field, $fieldValue, $fields, $options)
        ;
    }
}
