<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Security;

use Itq\Common\Service\PasswordService;
use Itq\Common\Traits;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AppAuthenticator extends AbstractGuardAuthenticator
{
    use Traits\ServiceTrait;
    use Traits\RouterAwareTrait;
    use Traits\ServiceAware\PasswordServiceAwareTrait;
    use Traits\ParameterAware\StartRouteParameterAwareTrait;
    use Traits\ParameterAware\RequiredRoleParameterAwareTrait;
    use Traits\ParameterAware\SuccessRouteParameterAwareTrait;
    use Traits\ParameterAware\FailureRouteParameterAwareTrait;
    use Traits\ParameterAware\LoginCheckUriParameterAwareTrait;
    use Traits\ParameterAware\UsernameFieldNameParameterAwareTrait;
    use Traits\ParameterAware\PasswordFieldNameParameterAwareTrait;
    use Traits\ParameterAware\SupportsRememberMeParameterAwareTrait;
    /**
     * @param RouterInterface $router
     * @param PasswordService $passwordService
     * @param string          $requiredRole
     * @param string          $loginCheckUri
     * @param string          $successRoute
     * @param string          $failureRoute
     * @param string          $startRoute
     * @param string          $usernameFieldName
     * @param string          $passwordFieldName
     * @param bool            $supportsRememberBe
     */
    public function __construct(
        RouterInterface $router,
        PasswordService $passwordService,
        $requiredRole = 'ROLE_ADMIN',
        $loginCheckUri = '/login_check',
        $successRoute = 'home',
        $failureRoute = 'security_login',
        $startRoute = 'security_login',
        $usernameFieldName = '_username',
        $passwordFieldName = '_password',
        $supportsRememberBe = false
    ) {
        $this->setRouter($router);
        $this->setPasswordService($passwordService);
        $this->setRequiredRole($requiredRole);
        $this->setLoginCheckUri($loginCheckUri);
        $this->setSuccessRoute($successRoute);
        $this->setFailureRoute($failureRoute);
        $this->setStartRoute($startRoute);
        $this->setUsernameFieldName($usernameFieldName);
        $this->setPasswordFieldName($passwordFieldName);
        $this->setSupportsRememberMe($supportsRememberBe);
    }
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $this->getLoginCheckUri() === $request->getPathInfo();
    }
    /**
     * @param Request $request
     *
     * @return array|null
     */
    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->request->get($this->getUsernameFieldName()),
            'password' => $request->request->get($this->getPasswordFieldName()),
        ];
    }
    /**
     * @param array                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }
    /**
     * @param array         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$this->getPasswordService()->test($credentials['password'], $user->getPassword(), ['salt' => $user->getSalt()])) {
            return false;
        }

        if (!in_array($this->getRequiredRole(), $user->getRoles())) {
            return false;
        }

        return true;
    }
    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return Response
     *
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->buildUrl($this->getSuccessRoute()));
    }
    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response
     *
     * @throws Exception
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($this->buildUrl($this->getFailureRoute()));
    }
    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return Response
     *
     * @throws Exception
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->buildUrl($this->getStartRoute()));
    }
    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return $this->getSupportsRememberMe();
    }
    /**
     * @param string|array $route
     *
     * @return string
     *
     * @throws Exception
     */
    protected function buildUrl($route)
    {
        if (!is_array($route)) {
            $route = [$route];
        }

        $route = array_values($route);

        switch (count($route)) {
            case 0:
                throw $this->createRequiredException('No route specified');
            case 1:
                $route[] = [];
                break;
        }

        return $this->getRouter()->generate($route[0], $route[1]);
    }
}
