<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Twig;

use Exception;
use Itq\Common\Traits;
use Twig_SimpleFilter;
use Itq\Common\Service;
use Twig_SimpleFunction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ItqExtension extends Base\AbstractExtension
{
    use Traits\TokenStorageAwareTrait;
    use Traits\ServiceAware\YamlServiceAwareTrait;
    use Traits\ServiceAware\TemplateServiceAwareTrait;
    use Traits\ServiceAware\ExceptionServiceAwareTrait;
    /**
     * @param array                    $variables
     * @param Service\ExceptionService $exceptionService
     * @param Service\TemplateService  $templateService
     * @param TokenStorageInterface    $tokenStorage
     * @param Service\YamlService      $yamlService
     */
    public function __construct(
        array $variables,
        Service\ExceptionService $exceptionService,
        Service\TemplateService $templateService,
        TokenStorageInterface $tokenStorage,
        Service\YamlService $yamlService
    ) {
        $this->setExceptionService($exceptionService);
        $this->setTokenStorage($tokenStorage);
        $this->setTemplateService($templateService);
        $this->setYamlService($yamlService);
        $this->setParameter('globals', $variables);
    }
    /**
     * @return array
     */
    public function getGlobals()
    {
        return ['ws' => $this->getArrayParameter('globals')]; // @todo rename 'ws' => 'itq'
    }
    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('base64_encode', [$this, 'getBase64EncodedString']),
            new Twig_SimpleFilter('describe_exception', [$this, 'getExceptionDescription']),
            new Twig_SimpleFilter('describe_request_input', [$this, 'getRequestInputDescription']),
            new Twig_SimpleFilter('describe_request_input', [$this, 'getRequestInputDescription']),
        ];
    }
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('classname', [$this, 'getClassName'], ['is_safe' => ['html']]),
        ];
    }
    /**
     * @param string $string
     *
     * @return string
     */
    public function getBase64EncodedString($string)
    {
        return base64_encode($string);
    }
    /**
     * @param Exception $e
     *
     * @return string
     */
    public function getExceptionDescription(Exception $e)
    {
        return $this->getYamlService()->serialize($this->getExceptionService()->describe($e), ['inlineLevel' => 10, 'indentSize' => 2]);
    }
    /**
     * @param Request $request
     *
     * @return string
     */
    public function getRequestInputDescription(Request $request)
    {
        $vars = [];
        foreach ($request->request->all() as $k => $v) {
            $v        = json_encode($v);
            $vars[$k] = ($this->getStringLength($v) > 4000) ? (substr($v, 0, 4000).'...') : json_decode($v);
        }

        return $this->getYamlService()->serialize($vars);
    }
    /**
     * @param mixed $v
     *
     * @return null|string
     */
    public function getClassName($v)
    {
        return is_object($v) ? get_class($v) : null;
    }
    /**
     * @param string $expression
     * @param array  $params
     *
     * @return string
     */
    public function renderInline($expression, array $params = [])
    {
        return $this->renderView('AppBundle::expression.txt.twig', ['_expression' => $expression] + $params);
    }
    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = [])
    {
        return $this->getTemplateService()->render($view, $parameters);
    }
    /**
     * @return mixed|null
     */
    public function getUser()
    {
        /** @var TokenInterface $token */
        $token = $this->getTokenStorage()->getToken();

        if (!$token) {
            return null;
        }

        return $token->getUser();
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'itq';
    }
}
