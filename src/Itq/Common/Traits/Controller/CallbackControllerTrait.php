<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CallbackControllerTrait
{
    /**
     * Renders a view.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */
    abstract public function render($view, array $parameters = [], Response $response = null);
    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status The status code to use for the Response
     *
     * @return Response
     */
    abstract public function redirect($url, $status = 302);
    /**
     * @param string          $message
     * @param \Exception|null $previous
     *
     * @return Exception
     */
    abstract public function createNotFoundException($message = 'Not Found', Exception $previous = null);
    /**
     * @param string $id
     *
     * @return object
     */
    abstract public function get($id);
    /**
     * @param string $locale
     *
     * @return $this
     */
    abstract protected function forceValidLocale($locale = null);
    /**
     * @param Request $request
     * @param string  $serviceId
     * @param string  $method
     * @param array   $params
     * @param string  $property
     * @param array   $options
     *
     * @return Response
     */
    protected function handleCallback(Request $request, $serviceId, $method, $params, $property, array $options = [])
    {
        $options += ['errorView' => 'ItqBundle:errors:generic.html.twig'];

        try {
            $this->forceValidLocale();
            foreach ($params as $k => $v) {
                $matches = null;
                if (is_string($v) && 0 < preg_match('/^\%([^\%]+)\%$/', $v, $matches)) {
                    $params[$k] = ('query_params' === $matches[1]) ? $request->query->all() : ($request->query->has($matches[1]) ? $request->query->get($matches[1]) : null);
                }
            }

            $result = call_user_func_array([$this->get($serviceId), $method], $params);

            if (!property_exists($result, $property) || !isset($result->$property)) {
                throw $this->createNotFoundException();
            }

            return $this->redirect($result->$property);
        } catch (Exception $e) {
            return $this->render($options['errorView'], ['exception' => $e]);
        }
    }
}
