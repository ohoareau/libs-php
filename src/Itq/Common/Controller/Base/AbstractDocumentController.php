<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Controller\Base;

use Itq\Common\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractDocumentController extends AbstractCrudController
{
    /**
     * Returns the implicit document service (based on class name)
     *
     * @param string $id
     *
     * @return Service\DocumentServiceInterface
     */
    protected function getService($id = null)
    {
        if (!$id) {
            $id = preg_replace('/Controller$/', '', basename(str_replace('\\', '/', get_class($this))));
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get('app.'.$id);
    }
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param Request $request
     * @param array    $options
     *
     * @return Response
     */
    protected function handleFind(Request $request, $options = [])
    {
        if ($this->getRequestService()->fetchQueryTotal($request)) {
            return $this->handleFindPage($request, $options);
        }

        return $this->returnFindResponse(
            $request,
            $this->getService()->find(
                $this->getRequestService()->fetchQueryCriteria($request),
                $this->getRequestService()->fetchQueryFields($request),
                $this->getRequestService()->fetchQueryLimit($request),
                $this->getRequestService()->fetchQueryOffset($request),
                $this->getRequestService()->fetchQuerySorts($request),
                $options
            ),
            $options
        );
    }
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param Request $request
     * @param array    $options
     *
     * @return Response
     */
    protected function handleFindPage(Request $request, $options = [])
    {
        $limit  = $this->getRequestService()->fetchQueryLimit($request);
        $offset = $this->getRequestService()->fetchQueryOffset($request);

        if (null === $limit) {
            $limit = 10;
        }

        $startTime = microtime(true);

        list ($items, $total) = $this->getService()->findWithTotal(
            $this->getRequestService()->fetchQueryCriteria($request),
            $this->getRequestService()->fetchQueryFields($request),
            $limit,
            $offset,
            $this->getRequestService()->fetchQuerySorts($request),
            $options
        );

        $endTime  = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 0);

        return $this->returnFindResponse(
            $request,
            [
                'items'     => $items,
                'count'     => count($items),
                'pageSize'  => $limit,
                'total'     => $total,
                'pages'     => ceil($total / $limit),
                'page'      => floor($offset / $limit),
                'duration'  => $duration,
                'offset'    => $offset,
                'startTime' => $startTime,
                'endTime'   => $endTime,
                'startDate' => (new \DateTime('@'.round($startTime, 0)))->format('c'),
                'endDate'   => (new \DateTime('@'.round($endTime, 0)))->format('c'),
            ],
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
        return $this->returnGetResponse(
            $request,
            $this->getService()->getBy(
                $field,
                $this->getRequestService()->fetchRouteParameter($request, $field),
                $this->getRequestService()->fetchQueryFields($request),
                $options
            ),
            $options
        );
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
     * Create a new document.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handleCreate(Request $request, $options = [])
    {
        if ($request->query->has('import')) {
            return $this->handleImport($request, $options);
        }

        if ($request->query->has('bulk')) {
            return $this->handleCreateBulk($request, $options);
        }

        return $this->returnCreateResponse(
            $request,
            $this->getService()->create(
                $this->getRequestService()->fetchRequestData($request),
                $options + ['user' => $this->getUser()]
            ),
            $options
        );
    }
    /**
     * Create a new document after ensuring it does not already exist.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handleEnsureSameOrNotExistAndCreate(Request $request, $options = [])
    {
        return $this->returnCreateResponse(
            $request,
            $this->getService()->ensureSameOrNotExistAndCreate(
                $this->getRequestService()->fetchRequestData($request),
                $options + ['user' => $this->getUser()]
            ),
            $options
        );
    }
    /**
     * Create documents in bulk.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handleCreateBulk(Request $request, $options = [])
    {
        return $this->returnCreateResponse(
            $request,
            $this->getService()->createBulk(
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
     * @param array   $options
     *
     * @return Response
     */
    protected function handleImport(Request $request, $options = [])
    {
        $rawData = $this->getRequestService()->fetchRequestData($request);

        if (!is_array($rawData) || 2 > count($rawData)) {
            throw new \RuntimeException('Malformed data', 412);
        }

        $rawData += ['settings' => [], 'data' => []];
        $data     = $rawData['data'];
        $settings = $rawData['settings'];
        unset($rawData);

        return $this->returnImportResponse($request, $this->getService()->import($data, $settings, $options), $options);
    }
    /**
     * @param Request $request
     * @param string  $firstBy
     * @param string  $secondBy
     * @param array   $options
     *
     * @return Response
     */
    protected function handleGetByAndBy(Request $request, $firstBy, $secondBy, array $options = [])
    {
        $queryFields = $this->getRequestService()->fetchQueryFields($request);
        $doc         = $this->getService()->getBy(
            $firstBy,
            $this->getRequestService()->fetchRouteParameter($request, $firstBy),
            !empty($queryFields) ? $queryFields + [$secondBy => true] : [],
            $options
        );

        if ($this->getRequestService()->fetchRouteParameter($request, $secondBy) !== $doc->$secondBy) {
            throw $this->createNotFoundException();
        }

        return $this->returnGetResponse($request, $doc);
    }
    /**
     * @param Request $request
     * @param string  $firstBy
     * @param string  $secondBy
     * @param array   $options
     *
     * @return Response
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
            throw $this->createNotFoundException('Doc exist but no associated %s', $otherField);
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
     * @param string  $property
     * @param string  $firstBy
     * @param string  $secondBy
     * @param array   $options
     *
     * @return Response
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
    /**
     * @param Request     $request
     * @param string      $field
     * @param string      $otherField
     * @param string      $bagClass
     * @param null|string $otherService
     * @param array       $options
     *
     * @return Response
     */
    protected function handleGetDoubleBagBy(Request $request, $field, $otherField, $bagClass, $otherService = null, array $options = [])
    {
        if (null === $otherService) {
            $otherService = $otherField;
        }

        $doc = $this->getService()->getBy(
            $field,
            $this->getRequestService()->fetchRouteParameter($request, $field),
            array_merge(['id' => true, $otherField => true], $this->getRequestService()->fetchQueryFields($request))
        );

        if (!$doc->$otherField) {
            throw $this->createNotFoundException('Doc exist but no associated %s', $otherField);
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
     *
     * @return Response
     */
    protected function handleUpdateByTokenAndStatusToken(Request $request)
    {
        return $this->handleUpdateByAndBy($request, 'token', 'statusToken');
    }
    /**
     * @param Request $request
     *
     * @return Response
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
