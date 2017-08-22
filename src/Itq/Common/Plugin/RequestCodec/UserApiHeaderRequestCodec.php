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

use Exception;
use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class UserApiHeaderRequestCodec extends Base\AbstractSecretApiHeaderRequestCodec
{
    use Traits\ServiceAware\UserProviderServiceAwareTrait;
    /**
     * @param Service\UserProviderService $userProviderService
     * @param string                      $secret
     */
    public function __construct(Service\UserProviderService $userProviderService, $secret = null)
    {
        parent::__construct(
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
     * @throws Exception
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
     * @return array
     */
    protected function getDefaultValues()
    {
        return ['id' => null, 'password' => null, 'expire' => null, 'token' => null];
    }
}
