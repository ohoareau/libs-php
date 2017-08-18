<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Service;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RequestService
{
    use Traits\ServiceTrait;
    use Traits\LoggerAwareTrait;
    use Traits\ClientProviderAwareTrait;
    use Traits\ServiceAware\TokenProviderServiceAwareTrait;
    use Traits\ServiceAware\UserProviderServiceAwareTrait;
    /**
     * @var string
     */
    protected $clientHeaderKey = 'X-Api-Client';
    /**
     * @var string
     */
    protected $userHeaderKey = 'X-Api-User';
    /**
     * @var string
     */
    protected $sudoHeaderKey = 'X-Api-Sudo';
    /**
     * @var string
     */
    protected $clientSecret = 'thisIsTheSuperLongSecret@Api2014!';
    /**
     * @var string
     */
    protected $userSecret = 'thisIsAnOtherTheSuperLongSecret@Api2014!';
    /**
     * @var string
     */
    protected $clientTokenCreationUriPattern = ',^.*/client\-tokens$,';
    /**
     * @var string
     */
    protected $userTokenCreationUriPattern = ',^.*/user\-tokens$,';
    /**
     * @var \Closure
     */
    protected $clientTokenCreationFunction;
    /**
     * @var \Closure
     */
    protected $userTokenCreationFunction;
    /**
     * @var \Closure
     */
    protected $clientHeaderParsingFunction;
    /**
     * @var \Closure
     */
    protected $userHeaderParsingFunction;
    /**
     * @var \Closure
     */
    protected $sudoHeaderParsingFunction;
    /**
     * @param Service\UserProviderService  $userProviderService
     * @param Service\TokenProviderService $tokenProviderService
     * @param string                       $clientSecret
     * @param string                       $userSecret
     */
    public function __construct(
        Service\UserProviderService $userProviderService,
        Service\TokenProviderService $tokenProviderService,
        $clientSecret = null,
        $userSecret = null
    ) {
        $this->setClientTokenCreationFunction(function ($id, $expire, $secret) {
            return base64_encode(sha1($id.$expire.$secret));
        });
        $this->setUserTokenCreationFunction(function ($id, $expire, $secret) {
            return base64_encode(sha1($id.$expire.$secret));
        });
        $this->setClientHeaderParsingFunction(function ($header) {
            if (is_array($header)) {
                $header = array_shift($header);
            }
            $parts = [];
            foreach (preg_split("/\\s*,\\s*/", trim($header)) as $t) {
                if (false === strpos($t, ':')) {
                    break;
                }
                list($key, $value) = explode(':', $t, 2);
                $key   = trim($key);
                $value = trim($value);
                if ($this->isNonEmptyString($value)) {
                    $parts[$key] = $value;
                }
            }

            return array_merge(['id' => null, 'expire' => null, 'token' => null], $parts);
        });
        $this->setUserHeaderParsingFunction(function ($header) {
            if (is_array($header)) {
                $header = array_shift($header);
            }
            $parts = [];
            foreach (preg_split("/\\s*,\\s*/", trim($header)) as $t) {
                if (false === strpos($t, ':')) {
                    break;
                }
                list($key, $value) = explode(':', $t, 2);
                $key   = trim($key);
                $value = trim($value);
                if ($this->isNonEmptyString($value)) {
                    $parts[$key] = $value;
                }
            }

            return array_merge(['id' => null, 'password' => null, 'expire' => null, 'token' => null], $parts);
        });
        $this->setSudoHeaderParsingFunction(function ($header) {
            if (is_array($header)) {
                $header = array_shift($header);
            }
            $parts = [];
            foreach (preg_split("/\\s*,\\s*/", trim($header)) as $t) {
                if (false === strpos($t, ':')) {
                    break;
                }
                list($key, $value) = explode(':', $t, 2);
                $key   = trim($key);
                $value = trim($value);
                if ($this->isNonEmptyString($value)) {
                    $parts[$key] = $value;
                }
            }

            return array_merge(['id' => null], $parts);
        });
        $this->setUserProviderService($userProviderService);
        $this->setTokenProviderService($tokenProviderService);

        if (null !== $clientSecret) {
            $this->setClientSecret($clientSecret);
        }

        if (null !== $userSecret) {
            $this->setUserSecret($userSecret);
        }
    }
    /**
     * @param string $clientHeaderKey
     *
     * @return $this
     */
    public function setClientHeaderKey($clientHeaderKey)
    {
        $this->clientHeaderKey = $clientHeaderKey;

        return $this;
    }
    /**
     * @return string
     */
    public function getClientHeaderKey()
    {
        return $this->clientHeaderKey;
    }
    /**
     * @param string $userHeaderKey
     *
     * @return $this
     */
    public function setUserHeaderKey($userHeaderKey)
    {
        $this->userHeaderKey = $userHeaderKey;

        return $this;
    }
    /**
     * @return string
     */
    public function getUserHeaderKey()
    {
        return $this->userHeaderKey;
    }
    /**
     * @param string $sudoHeaderKey
     *
     * @return $this
     */
    public function setSudoHeaderKey($sudoHeaderKey)
    {
        $this->sudoHeaderKey = $sudoHeaderKey;

        return $this;
    }
    /**
     * @return string
     */
    public function getSudoHeaderKey()
    {
        return $this->sudoHeaderKey;
    }
    /**
     * @param string $clientSecret
     *
     * @return $this
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }
    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }
    /**
     * @param string $userSecret
     *
     * @return $this
     */
    public function setUserSecret($userSecret)
    {
        $this->userSecret = $userSecret;

        return $this;
    }
    /**
     * @return string
     */
    public function getUserSecret()
    {
        return $this->userSecret;
    }
    /**
     * @param string $clientTokenCreationUriPattern
     *
     * @return $this
     */
    public function setClientTokenCreationUriPattern($clientTokenCreationUriPattern)
    {
        $this->clientTokenCreationUriPattern = $clientTokenCreationUriPattern;

        return $this;
    }
    /**
     * @return string
     */
    public function getClientTokenCreationUriPattern()
    {
        return $this->clientTokenCreationUriPattern;
    }
    /**
     * @param string $userTokenCreationUriPattern
     *
     * @return $this
     */
    public function setUserTokenCreationUriPattern($userTokenCreationUriPattern)
    {
        $this->userTokenCreationUriPattern = $userTokenCreationUriPattern;

        return $this;
    }
    /**
     * @return string
     */
    public function getUserTokenCreationUriPattern()
    {
        return $this->userTokenCreationUriPattern;
    }
    /**
     * @param \Closure $clientHeaderParsingFunction
     *
     * @return $this
     */
    public function setClientHeaderParsingFunction($clientHeaderParsingFunction)
    {
        $this->clientHeaderParsingFunction = $clientHeaderParsingFunction;

        return $this;
    }
    /**
     * @return \Closure
     */
    public function getClientHeaderParsingFunction()
    {
        return $this->clientHeaderParsingFunction;
    }
    /**
     * @param \Closure $userHeaderParsingFunction
     *
     * @return $this
     */
    public function setUserHeaderParsingFunction($userHeaderParsingFunction)
    {
        $this->userHeaderParsingFunction = $userHeaderParsingFunction;

        return $this;
    }
    /**
     * @return \Closure
     */
    public function getUserHeaderParsingFunction()
    {
        return $this->userHeaderParsingFunction;
    }
    /**
     * @param \Closure $sudoHeaderParsingFunction
     *
     * @return $this
     */
    public function setSudoHeaderParsingFunction($sudoHeaderParsingFunction)
    {
        $this->sudoHeaderParsingFunction = $sudoHeaderParsingFunction;

        return $this;
    }
    /**
     * @return \Closure
     */
    public function getSudoHeaderParsingFunction()
    {
        return $this->sudoHeaderParsingFunction;
    }
    /**
     * @param \Closure $clientTokenCreationFunction
     *
     * @return $this
     */
    public function setClientTokenCreationFunction($clientTokenCreationFunction)
    {
        $this->clientTokenCreationFunction = $clientTokenCreationFunction;

        return $this;
    }
    /**
     * @return \Closure
     */
    public function getClientTokenCreationFunction()
    {
        return $this->clientTokenCreationFunction;
    }
    /**
     * @param \Closure $userTokenCreationFunction
     *
     * @return $this
     */
    public function setUserTokenCreationFunction($userTokenCreationFunction)
    {
        $this->userTokenCreationFunction = $userTokenCreationFunction;

        return $this;
    }
    /**
     * @return \Closure
     */
    public function getUserTokenCreationFunction()
    {
        return $this->userTokenCreationFunction;
    }
    /**
     * @param Request $request
     *
     * @return array
     */
    public function parse(Request $request)
    {
        return [
            'client' => $this->getRequestClient($request),
            'user'   => $this->getRequestUser($request),
            'sudo'   => $this->getRequestSudo($request),
        ];
    }
    /**
     * @param string $id
     * @param string $expire
     * @param string $secret
     *
     * @return string
     */
    public function buildClientToken($id, $expire, $secret)
    {
        $function = $this->getClientTokenCreationFunction();

        return $function($id, $expire, $secret);
    }
    /**
     * @param string $id
     * @param string $expire
     * @param string $secret
     *
     * @return string
     */
    public function buildUserToken($id, $expire, $secret)
    {
        $function = $this->getUserTokenCreationFunction();

        return $function($id, $expire, $secret);
    }
    /**
     * @param \DateTime $date
     * @param \DateTime $expirationDate
     *
     * @return bool
     */
    public function isDateExpired(\DateTime $date, \DateTime $expirationDate)
    {
        return $date > $expirationDate;
    }
    /**
     * @param Request $request
     *
     * @return string
     */
    public function createClientTokenFromRequestAndReturnHeaders(Request $request)
    {
        $headers = $request->headers->all();

        if (!isset($headers[strtolower($this->getClientHeaderKey())])) {
            $headers[strtolower($this->getClientHeaderKey())] = null;
        }

        $function = $this->getClientHeaderParsingFunction();
        $parts    = $function($headers[strtolower($this->getClientHeaderKey())]);

        $now    = new \DateTime();
        $expire = $now->add(new \DateInterval('P1D'));

        return $this->buildGenericTokenExpirableHeaders(
            $this->getClientHeaderKey(),
            $parts['id'],
            $expire,
            $this->createClientToken($parts['id'], $expire)
        );
    }
    /**
     * @param Request $request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function createUserTokenFromRequestAndReturnHeaders(Request $request)
    {
        $headers = $request->headers->all();

        if (!isset($headers[strtolower($this->getClientHeaderKey())])) {
            throw $this->createAuthorizationRequiredException('auth.header.missing_client');
        }

        if (!isset($headers[strtolower($this->getUserHeaderKey())])) {
            throw $this->createAuthorizationRequiredException('auth.header.missing_user');
        }

        $function = $this->getUserHeaderParsingFunction();
        $parts    = $function($headers[strtolower($this->getUserHeaderKey())]);

        $now    = new \DateTime();
        $expire = $now->add(new \DateInterval('P1D'));

        if (!isset($parts['id'])) {
            throw $this->createAuthorizationRequiredException('auth.header.missing_user_id');
        }

        $account = $this->getUserProviderService()->getAccount($parts['id']);

        if (!isset($account['*alreadyAuthentified*']) || true !== $account['*alreadyAuthentified*']) {
            if (!isset($parts['password'])) {
                throw $this->createAuthorizationRequiredException('auth.header.missing_user_password');
            }

            $password = null;

            if (true === isset($account['password'])) {
                $password = (string) $account['password'];
            } elseif (true === isset($account['secret'])) {
                $password = (string) $account['secret'];
            }

            $expectedEncodedPassword = $password;
            $actualEncodedPassword   = $parts['password'];

            if ($expectedEncodedPassword !== $actualEncodedPassword) {
                throw $this->createDeniedException("auth.header.malformed_user_password", $parts['id']);
            }
        }

        return $this->buildGenericTokenExpirableHeaders(
            $this->getUserHeaderKey(),
            $parts['id'],
            $expire,
            $this->createUserToken($parts['id'], $expire)
        );
    }
    /**
     * @param Request $request
     * @param string  $type
     *
     * @return string
     *
     * @throws \Exception
     */
    public function createTokenFromRequest(Request $request, $type)
    {
        $headers = $request->headers->all();

        if (!isset($headers[strtolower($this->getClientHeaderKey())])) {
            throw $this->createAuthorizationRequiredException('auth.header.missing_client');
        }

        return $this->getTokenProviderService()->generate($type, $request->request->all());
    }
    /**
     * @param Request $request
     * @return array
     */
    public function fetchQueryCriteria(Request $request)
    {
        $v = $request->get('criteria', []);

        if (!is_array($v)) {
            $v = [];
        }

        return $v;
    }
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return array
     */
    public function fetchQueryFields(Request $request, array $options = [])
    {
        $options += ['key' => 'fields'];

        $v = $request->get($options['key'], []);

        if (!is_array($v) || !count($v)) {
            return [];
        }

        $fields = [];

        foreach ($v as $field) {
            if ('!' === substr($field, 0, 1)) {
                $fields[substr($field, 1)] = false;
            } else {
                $fields[$field] = true;
            }
        }

        return $fields;
    }
    /**
     * @param Request $request
     *
     * @return null|int
     */
    public function fetchQueryLimit(Request $request)
    {
        $v = $request->get('limit', null);

        return $this->isNonEmptyString($v) ? intval($v) : null;
    }
    /**
     * @param Request $request
     *
     * @return int
     */
    public function fetchQueryOffset(Request $request)
    {
        $v = intval($request->get('offset', 0));

        return 0 > $v ? 0 : $v;
    }
    /**
     * @param Request $request
     *
     * @return int
     */
    public function fetchQueryTotal(Request $request)
    {
        return null !== $request->get('total', null);
    }
    /**
     * @param Request $request
     *
     * @return array
     */
    public function fetchQuerySorts(Request $request)
    {
        $v = $request->get('sorts', []);

        if (!is_array($v) || !count($v)) {
            return [];
        }

        return array_map(
            function ($a) {
                return (int) $a;
            },
            $v
        );
    }
    /**
     * @param Request $request
     *
     * @return array
     */
    public function fetchRequestData(Request $request)
    {
        return $request->request->all();
    }
    /**
     * @param Request $request
     * @param string  $parameter
     *
     * @return mixed
     */
    public function fetchRouteParameter(Request $request, $parameter)
    {
        return $request->attributes->get($parameter);
    }
    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function getRequestClient(Request $request)
    {
        $function = $this->getClientHeaderParsingFunction();

        return $function($request->headers->get(strtolower($this->getClientHeaderKey())));
    }
    /**
     * @param Request $request
     *
     * @return string
     */
    protected function getRequestUser(Request $request)
    {
        $function = $this->getUserHeaderParsingFunction();

        return $function($request->headers->get(strtolower($this->getUserHeaderKey())));
    }
    /**
     * @param Request $request
     *
     * @return string
     */
    protected function getRequestSudo(Request $request)
    {
        $function = $this->getSudoHeaderParsingFunction();

        return $function($request->headers->get(strtolower($this->getSudoHeaderKey())));
    }
    /**
     * @param \DateTime $expire
     *
     * @return string
     */
    protected function convertDateTimeToString(\DateTime $expire)
    {
        return $expire->format(\DateTime::ISO8601);
    }
    /**
     * @param string    $id
     * @param \DateTime $expire
     *
     * @return string
     */
    protected function createClientToken($id, \DateTime $expire)
    {
        $this->getClientProvider()->get($id, ['id']);

        return $this->buildClientToken($id, $this->convertDateTimeToString($expire), $this->getClientSecret());
    }
    /**
     * @param string    $id
     * @param \DateTime $expire
     *
     * @return string
     */
    protected function createUserToken($id, \DateTime $expire)
    {
        return $this->buildUserToken($id, $this->convertDateTimeToString($expire), $this->getUserSecret());
    }
    /**
     * @param string    $headerKey
     * @param string    $id
     * @param \DateTime $expire
     * @param string    $token
     *
     * @return array
     */
    protected function buildGenericTokenExpirableHeaders($headerKey, $id, \DateTime $expire, $token)
    {
        return [
            $headerKey => sprintf('id: %s, expire: %s, token: %s', $id, $this->convertDateTimeToString($expire), $token),
        ];
    }
}
