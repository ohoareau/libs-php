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
trait UpdateDocumentControllerTrait
{
    /**
     * @return mixed
     *
     * @throws Exception
     */
    abstract public function getUser();
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
    abstract protected function handleCreate(Request $request, $options = []);
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function returnUpdateResponse(Request $request, $data = null, array $options = []);
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function returnGetResponse(Request $request, $data = null, array $options = []);
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
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchRequestData($request),
                $options + ['user' => $this->getUser()]
            ),
            $options
        );
    }
    /**
     * Update the specified document.
     *
     * @param Request $request
     * @param string  $field
     * @param array   $options
     *
     * @return Response
     */
    protected function handleUpdateBy(Request $request, $field, $options = [])
    {
        return $this->returnUpdateResponse(
            $request,
            $this->getService()->updateBy(
                $field,
                $this->getRequestService()->fetchRouteParameter($request, $field),
                $this->getRequestService()->fetchRequestData($request),
                $options + ['user' => $this->getUser()]
            ),
            $options
        );
    }
    /**
     * Reset the specified document property.
     *
     * @param Request $request
     * @param string  $property
     * @param array   $options
     *
     * @return Response
     */
    protected function handleResetProperty(Request $request, $property, $options = [])
    {
        $service = $this->getService();
        $id      = $this->getRequestService()->fetchRouteParameter($request, 'id');

        if (method_exists($service, 'reset'.ucfirst($property))) {
            $data = $service->{'reset'.ucfirst($property)}($id);
        } else {
            $data = $service->update($id, [$property => '*cleared*']);
        }

        return $this->returnUpdateResponse($request, $data, $options);
    }
    /**
     * Reset the specified document property.
     *
     * @param Request $request
     * @param string  $property
     * @param string  $key
     * @param array   $options
     *
     * @return Response
     */
    protected function handleResetPropertyBy(Request $request, $property, $key, $options = [])
    {
        $service = $this->getService();
        $value   = $this->getRequestService()->fetchRouteParameter($request, $key);

        if (method_exists($service, 'reset'.ucfirst($property).'By'.ucfirst($key))) {
            $data = $service->{'reset'.ucfirst($property).'By'.$key}($value);
        } else {
            $data = $service->updateBy($key, $value, [$property => '*cleared*']);
        }

        return $this->returnUpdateResponse($request, $data, $options);
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
    protected function handleUpdateByAndBy(Request $request, $firstBy, $secondBy, array $options = [])
    {
        $firstByValue  = $this->getRequestService()->fetchRouteParameter($request, $firstBy);
        $secondByValue = $this->getRequestService()->fetchRouteParameter($request, $secondBy);

        if (isset($options['decodeSecondByValue'])) {
            $secondByValue = @base64_decode($secondByValue);

            if (!$secondByValue) {
                throw $this->createNotFoundException();
            }
        }

        $doc = $this->getService()->findOne(
            [
                $firstBy  => $firstByValue,
                $secondBy => $secondByValue,
            ],
            array_merge(
                ['id' => true],
                isset($options['extraFields']) ? $options['extraFields'] : [],
                $this->getRequestService()->fetchQueryFields($request)
            ),
            0,
            [],
            $options
        );

        if (null === $doc) {
            throw $this->createNotFoundException();
        }

        $this->getService()->update($doc->id, $this->getRequestService()->fetchRequestData($request), $options);

        return $this->returnGetResponse($request, $doc);
    }
    /**
     * @param Request     $request
     * @param string      $firstBy
     * @param string      $secondBy
     * @param string      $otherField
     * @param string      $bagClass
     * @param null|string $otherService
     * @param array       $options
     *
     * @return Response
     *
     * @throws Exception
     */
    protected function handleUpdateByAndByAndReturnDoubleBag(Request $request, $firstBy, $secondBy, $otherField, $bagClass, $otherService = null, array $options = [])
    {
        if (null === $otherService) {
            $otherService = $otherField;
        }

        $firstByValue = $this->getRequestService()->fetchRouteParameter($request, $firstBy);
        $doc          = $this->getService()->findOne(
            [
                $firstBy  => $firstByValue,
                $secondBy => $this->getRequestService()->fetchRouteParameter($request, $secondBy),
            ],
            array_merge(['id' => true, $otherField => true], $this->getRequestService()->fetchQueryFields($request)),
            0,
            [],
            $options
        );

        if (null === $doc) {
            throw $this->createNotFoundException();
        }

        if (!$doc->$otherField) {
            throw $this->createNotFoundException(sprintf('Doc exist but no associated %s', $otherField));
        }

        $this->getService()->update($doc->id, $this->getRequestService()->fetchRequestData($request), $options);

        return $this->returnGetResponse(
            $request,
            new $bagClass(
                $this->getService($otherService)->get(
                    $doc->$otherField,
                    $this->getRequestService()->fetchQueryFields($request, ['key' => sprintf('%sFields', $otherField)])
                ),
                $doc
            )
        );
    }
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    protected function handleUpdateByTokenAndStatusToken(Request $request)
    {
        return $this->handleUpdateByAndBy($request, 'token', 'statusToken');
    }
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    protected function handleUpdateByTokenAndAdminToken(Request $request)
    {
        return $this->handleUpdateByAndBy($request, 'token', 'adminToken');
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleUpdateByProgressToken(Request $request)
    {
        return $this->handleUpdateBy($request, 'progressToken');
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleUpdateByStatusToken(Request $request)
    {
        return $this->handleUpdateBy($request, 'statusToken');
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleUpdateByToken(Request $request)
    {
        return $this->handleUpdateBy($request, 'token');
    }
    /**
     * @param Request $request
     * @param array   $fields
     *
     * @return Response
     */
    protected function handleCreateOrUpdateByData(Request $request, array $fields)
    {
        $data    = $this->getRequestService()->fetchRequestData($request);
        $service = $this->getService();
        $doc     = $service->findOneByData($data, $fields);

        if (null !== $doc) {
            foreach ($fields as $field) {
                if (isset($data[$field]) && $doc->$field === $data[$field]) {
                    unset($data[$field]);
                }
            }

            return $this->returnUpdateResponse($request, $this->getService()->update($doc->id, $data));
        }

        return $this->handleCreate($request);
    }
}
