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
use Exception as PhpException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TimeApiHeaderRequestCodec extends Base\AbstractSecretApiHeaderRequestCodec
{
    use Traits\DateProviderAwareTrait;
    /**
     * @param Service\DateService $dateService
     * @param string              $secret
     * @param string              $creationSecret
     */
    public function __construct(Service\DateService $dateService, $secret = null, $creationSecret = null)
    {
        parent::__construct($dateService, 'X-Api-Time', $secret ?: 'thisIsTheSuperLongSecret@Api2014!');
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
            throw $this->createAuthorizationRequiredException('auth.header.missing_time_set');
        }
        if (!isset($parts['password'])) {
            throw $this->createAuthorizationRequiredException('auth.header.missing_time_password');
        }

        $creationSecret = $this->getCreationSecret();

        if (null !== $creationSecret) {
            $expectedPassword = md5(sha1(md5(sha1(md5(sha1($parts['id'].$creationSecret)))).$parts['id']));

            if ($expectedPassword !== $parts['password']) {
                throw $this->createDeniedException("auth.header.malformed_time_password", $parts['id']);
            }
        }

        $data = array_intersect_key($parts, ['id' => true]);

        $this->getDateProvider()->checkDateStringFormat($data['id']);

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
            throw new Exception\BadTimeTokenException();
        }
        if ($this->getDateService()->isDateExpiredFromNow($expire)) {
            throw new Exception\BadTimeTokenException();
        }

        $this->getDateProvider()->setCurrentDateFromString($parts['id']);

        return $parts;
    }
    /**
     * @return array
     */
    protected function getDefaultValues()
    {
        return ['id' => null, 'expire' => null, 'token' => null];
    }
}
