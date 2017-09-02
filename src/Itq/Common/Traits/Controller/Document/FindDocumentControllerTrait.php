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

use DateTime;
use Itq\Common\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait FindDocumentControllerTrait
{
    /**
     * @return Service\RequestService
     */
    abstract public function getRequestService();
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
    abstract protected function returnFindResponse(Request $request, $data = null, array $options = []);
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
     * @param array   $options
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
                'startDate' => (new DateTime('@'.round($startTime, 0)))->format('c'),
                'endDate'   => (new DateTime('@'.round($endTime, 0)))->format('c'),
            ],
            $options
        );
    }
}
