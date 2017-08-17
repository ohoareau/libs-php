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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractCrudController extends AbstractController
{
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    protected function filterData($data)
    {
        return $this->getDataFilterService()->filter($data);
    }
    /**
     * Returns the http response.
     *
     * @param mixed   $data
     * @param int     $code
     * @param array   $headers
     * @param array   $options
     * @param Request $request
     *
     * @return Response
     */
    protected function returnResponse($data = null, $code = 200, $headers = [], $options = [], Request $request = null)
    {
        return $this->getResponseService()->create(
            isset($request) && count($request->getAcceptableContentTypes()) ? $request->getAcceptableContentTypes() : [['value' => 'application/json']],
            $this->filterData($data),
            $code,
            $headers,
            $options
        );
    }
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    protected function returnGetResponse(Request $request, $data = null, array $options = [])
    {
        return $this->returnResponse($data, 200, [], ['groups' => ['Default', 'detailed']] + $options, $request);
    }
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    protected function returnFindResponse(Request $request, $data = null, array $options = [])
    {
        return $this->returnResponse($data, 200, [], ['groups' => ['Default', 'listed']] + $options, $request);
    }
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    protected function returnUpdateResponse(Request $request, $data = null, array $options = [])
    {
        return $this->returnResponse($data, 200, [], ['groups' => ['Default', 'updated']] + $options, $request);
    }
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    protected function returnCreateResponse(Request $request, $data = null, array $options = [])
    {
        return $this->returnResponse($data, 201, [], ['groups' => ['Default', 'created']] + $options, $request);
    }
    /**
     * @param Request $request
     * @param mixed   $data
     * @param array   $options
     *
     * @return Response
     */
    protected function returnImportResponse(Request $request, $data = null, array $options = [])
    {
        return $this->returnResponse($data, 201, [], ['groups' => ['Default', 'imported']] + $options, $request);
    }
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function returnPurgeResponse(Request $request, array $options = [])
    {
        return $this->returnResponse(null, 204, [], $options, $request);
    }
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return Response
     */
    protected function returnDeleteResponse(Request $request, array $options = [])
    {
        return $this->returnResponse(null, 204, [], $options, $request);
    }
    /**
     * @param Request $request
     * @param string  $service
     * @param string  $method
     * @param array   $params
     *
     * @return Response
     */
    protected function handleServiceCall(Request $request, $service, $method, array $params = [])
    {
        foreach ($params as $k => $v) {
            $matches = null;
            if (!is_string($v) || 0 >= preg_match('/^\%([^\%]+)\%$/', $v, $matches)) {
                continue;
            }
            $params[$k] = $this->getRequestService()->fetchRouteParameter($request, $matches[1]);
        }

        return $this->returnGetResponse($request, call_user_func_array([$this->get($service), $method], $params));
    }
    /**
     * @param string $locale
     *
     * @return $this
     */
    protected function forceValidLocale($locale = null)
    {
        try {
            if (null === $locale) {
                if (!$this->hasParameter('locale')) {
                    return $this;
                }
                $locale = $this->getParameter('locale');
            }

            if ($this->has('request_stack')) {
                $requestStack = $this->getRequestStack();

                if (null !== $requestStack->getMasterRequest()) {
                    $requestStack->getMasterRequest()->setLocale($locale);
                }
            }
        } catch (\Exception $e) {
            // locale unchanged
        }

        return $this;
    }
}
