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

use Itq\Common\Service as CommonService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractSubDocumentController extends AbstractCrudController
{
    /**
     * Returns the implicit document service (based on class name)
     *
     * @return CommonService\SubDocumentServiceInterface
     */
    protected function getService()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get(
            'app.'.preg_replace('/controller$/', '', join('.', array_slice(explode('.', str_replace('\\', '.', strtolower(get_class($this)))), -2)))
        );
    }
    /**
     * Retrieve the documents matching the specified criteria.
     *
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function handleFind(Request $request, $options = [])
    {
        if ($this->getRequestService()->fetchQueryTotal($request)) {
            return $this->handleFindPage($request, $options);
        }

        return $this->returnResponse(
            $this->getService()->find(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchQueryCriteria($request),
                $this->getRequestService()->fetchQueryFields($request),
                $this->getRequestService()->fetchQueryLimit($request),
                $this->getRequestService()->fetchQueryOffset($request),
                $this->getRequestService()->fetchQuerySorts($request),
                $options
            ),
            200,
            [],
            ['groups' => ['Default', 'listed']],
            $request
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

        $startTime = microtime(true);

        list ($items, $total) = $this->getService()->findWithTotal(
            $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
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
     * Retrieve the documents matching the specified criteria.
     *
     * @param Request $request
     * @param string  $field
     * @param array   $options
     *
     * @return Response
     */
    protected function handleFindBy(Request $request, $field, $options = [])
    {
        return $this->returnResponse(
            $this->getService()->findBy(
                $field,
                $this->getRequestService()->fetchRouteParameter($request, $field),
                $this->getRequestService()->fetchQueryCriteria($request),
                $this->getRequestService()->fetchQueryFields($request),
                $this->getRequestService()->fetchQueryLimit($request),
                $this->getRequestService()->fetchQueryOffset($request),
                $this->getRequestService()->fetchQuerySorts($request),
                $options
            ),
            200,
            [],
            ['groups' => ['Default', 'listed']],
            $request
        );
    }
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
        return $this->returnResponse(
            $this->getService()->get(
                $this->getService()->getParentIdBy($parentFieldName, $this->getRequestService()->fetchRouteParameter($request, $parentFieldName)),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchQueryFields($request),
                $options
            ),
            200,
            [],
            ['groups' => ['Default', 'detailed']],
            $request
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
        return $this->returnResponse(
            $this->getService()->getProperty(
                $this->getService()->getParentIdBy($parentFieldName, $this->getRequestService()->fetchRouteParameter($request, $parentFieldName)),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $property,
                $options
            ),
            200,
            [],
            ['groups' => ['Default', 'detailed']] + $options,
            $request
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
        return $this->returnResponse(
            $this->getService()->get(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchQueryFields($request),
                $options
            ),
            200,
            [],
            ['groups' => ['Default', 'detailed']],
            $request
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
            $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
            $this->getRequestService()->fetchRouteParameter($request, 'id'),
            $options
        );

        return $this->returnResponse(null, 204, [], [], $request);
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

        return $this->returnResponse(null, 204, [], [], $request);
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

        return $this->returnDeleteResponse(
            $request,
            $options
        );
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
        return $this->returnResponse(
            $this->getService()->update(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            200,
            [],
            ['groups' => ['Default', 'updated']],
            $request
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
        return $this->returnResponse(
            $this->getService()->update(
                $this->getService()->getParentIdBy($parentFieldName, $this->getRequestService()->fetchRouteParameter($request, $parentFieldName)),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            200,
            [],
            ['groups' => ['Default', 'updated']],
            $request
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
     */
    protected function handleUpdateProperty(Request $request, $property, $options = [])
    {
        $service = $this->getService();
        $method  = 'update'.ucfirst($property);

        if (!method_exists($service, $method)) {
            throw $this->createAccessDeniedException();
        }

        return $this->returnResponse(
            $service->$method(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRouteParameter($request, 'id'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            200,
            [],
            ['groups' => ['Default', 'updated']],
            $request
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
    protected function handleCreate(Request $request, $options = [])
    {
        return $this->returnResponse(
            $this->getService()->create(
                $this->getRequestService()->fetchRouteParameter($request, 'parentId'),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            201,
            [],
            ['groups' => ['Default', 'created']],
            $request
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
        return $this->returnResponse(
            $this->getService()->createBy(
                $field,
                $this->getRequestService()->fetchRouteParameter($request, $field),
                $this->getRequestService()->fetchRequestData($request),
                $options
            ),
            201,
            [],
            ['groups' => ['Default', 'created']],
            $request
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
    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function handleFindByToken(Request $request)
    {
        return $this->handleFindBy($request, 'token');
    }
}
