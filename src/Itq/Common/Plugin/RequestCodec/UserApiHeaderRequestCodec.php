<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\RequestCodec;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\Exception;
use Exception as BaseException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class UserApiHeaderRequestCodec extends Base\AbstractSecretApiHeaderRequestCodec
{
    use Traits\ServiceAware\UserProviderServiceAwareTrait;
    /**
     * @param Service\DateService         $dateService
     * @param Service\UserProviderService $userProviderService
     * @param string                      $secret
     */
    public function __construct(
        Service\DateService $dateService,
        Service\UserProviderService $userProviderService,
        $secret = null
    ) {
        parent::__construct(
            $dateService,
            'X-Api-User',
            $secret ?: 'thisIsAnOtherTheSuperLongSecret@Api2014!',
            [
                'X-Api-Client' => 'auth.header.missing_client',
                'X-Api-User'   => 'auth.header.missing_user',
            ]
        );
        $this->setUserProviderService($userProviderService);
    }
    /**
     * @param array $parts
     * @param array $options
     *
     * @throws BaseException
     */
    protected function processEncoding(array $parts, array $options = [])
    {
        if (!isset($parts['id'])) {
            throw $this->createAuthorizationRequiredException('auth.header.missing_user_id');
        }

        $account = $this->getUserProviderService()->getAccount($parts['id']);

        if (!isset($account['*alreadyAuthentified*']) || true !== $account['*alreadyAuthentified*']) {
            if (!isset($parts['password'])) {
                throw $this->createAuthorizationRequiredException('auth.header.missing_user_password');
            }

            $expectedEncodedPassword = isset($account['password']) ? ((string) $account['password']) : (isset($account['secret']) ? ((string) $account['secret']) : null);

            if ($expectedEncodedPassword !== $parts['password']) {
                throw $this->createDeniedException("auth.header.malformed_user_password", $parts['id']);
            }
        }
    }
    /**
     * @param array $parts
     * @param array $options
     *
     * @return array
     */
    protected function processDecoding(array $parts, array $options = [])
    {
        $id     = $parts['id'];
        $token  = $parts['token'];
        $expire = $this->getDateService()->convertStringToDateTime($parts['expire']);

        if (null === $id) {
            throw new Exception\MissingUserIdentityException();
        }
        if ($token !== $this->stamp($id, $expire)) {
            throw new Exception\BadClientTokenException();
        }
        if ($this->getDateService()->isDateExpiredFromNow($expire)) {
            throw new Exception\BadUserTokenException();
        }

        return $parts;
    }
    /**
     * @return array
     */
    protected function getDefaultValues()
    {
        return ['id' => null, 'password' => null, 'expire' => null, 'token' => null];
    }
}
