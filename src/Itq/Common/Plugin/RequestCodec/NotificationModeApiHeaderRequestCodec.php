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

use Exception as PhpException;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NotificationModeApiHeaderRequestCodec extends Base\AbstractSecretApiHeaderRequestCodec
{
    use Traits\NotificationModeProviderAwareTrait;
    /**
     * @param Service\DateService $dateService
     * @param string              $secret
     * @param string              $creationSecret
     */
    public function __construct(Service\DateService $dateService, $secret = null, $creationSecret = null)
    {
        parent::__construct($dateService, 'X-Api-Notification-Mode', $secret ?: 'thisIsTheSuperLongSecret@Api2014!');
        $this->setCreationSecret($creationSecret);
    }
    /**
     * @param string|null $creationSecret
     *
     * @return $this
     */
    public function setCreationSecret($creationSecret)
    {
        return $this->setParameter('creationSecret', $creationSecret);
    }
    /**
     * @return string|null
     */
    public function getCreationSecret()
    {
        return $this->getParameterIfExists('creationSecret');
    }
    /**
     * @param array $parts
     * @param array $options
     *
     * @return void
     *
     * @throws PhpException
     */
    protected function processEncoding(array $parts, array $options = [])
    {
        if (!isset($parts['id'])) {
            throw $this->createAuthorizationRequiredException('auth.header.missing_notificationmode_set');
        }
        if (!isset($parts['password'])) {
            throw $this->createAuthorizationRequiredException('auth.header.missing_notificationmode_password');
        }

        $creationSecret = $this->getCreationSecret();

        if (null !== $creationSecret) {
            $expectedPassword = md5(sha1(md5(sha1(md5(sha1($parts['id'].$creationSecret)))).$parts['id']));

            if ($expectedPassword !== $parts['password']) {
                throw $this->createDeniedException("auth.header.malformed_notificationmode_password", $parts['id']);
            }
        }

        $data = array_intersect_key($parts, ['id' => true]);

        $this->parseSetValue($data['id']);

        unset($parts, $data);
    }
    /**
     * @param array $parts
     * @param array $options
     *
     * @return array|null
     */
    protected function processDecoding(array $parts, array $options = [])
    {
        $id = $parts['id'];

        if (null === $id) {
            return null;
        }

        $token  = $parts['token'];
        $expire = $this->getDateService()->convertStringToDateTime($parts['expire']);

        if ($token !== $this->stamp($id, $expire)) {
            throw new Exception\BadNotificationTokenException();
        }
        if ($this->getDateService()->isDateExpiredFromNow($expire)) {
            throw new Exception\BadNotificationTokenException();
        }

        $parts['id'] = $this->parseSetValue($parts['id']);

        foreach ($parts['id'] as $type => $mode) {
            $this->getNotificationModeProvider()->setTypeMode($type, $mode);
        }

        return $parts;
    }
    /**
     * @param string $value
     *
     * @return array
     *
     * @throws PhpException
     */
    protected function parseSetValue($value)
    {
        if (0 >= preg_match('/^([a-z0-9_]+\=[a-z0-9]+\&)*[a-z0-9_]+\=[a-z0-9]+$/i', $value)) {
            throw $this->createMalformedException('auth.header.malformed_notificationmode_set', $value);
        }

        $types = [];

        foreach (explode('&', $value) as $token) {
            list ($type, $mode) = explode('=', $token, 2);

            $types[$type] = $mode;
        }

        return $types;
    }
    /**
     * @return array
     */
    protected function getDefaultValues()
    {
        return ['id' => null, 'expire' => null, 'token' => null];
    }
}
