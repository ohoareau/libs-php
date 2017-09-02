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
trait CreateDocumentControllerTrait
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
     * @param string $msg
     * @param array  ...$params
     *
     * @return Exception
     */
    abstract protected function createMalformedException($msg, ...$params);
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
    abstract protected function returnCreateResponse(Request $request, $data = null, array $options = []);
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    abstract protected function returnImportResponse(Request $request, $data = null, array $options = []);
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
     *
     * @throws Exception
     */
    protected function handleImport(Request $request, $options = [])
    {
        $rawData = $this->getRequestService()->fetchRequestData($request);

        if (!is_array($rawData) || 2 > count($rawData)) {
            throw $this->createMalformedException('Malformed data');
        }

        $rawData += ['settings' => [], 'data' => []];
        $data     = $rawData['data'];
        $settings = $rawData['settings'];
        unset($rawData);

        return $this->returnImportResponse($request, $this->getService()->import($data, $settings, $options), $options);
    }
}
