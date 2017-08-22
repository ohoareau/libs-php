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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ClientApiHeaderRequestCodec extends Base\AbstractSecretApiHeaderRequestCodec
{
    use Traits\ClientProviderAwareTrait;
    /**
     * @param Service\DateService $dateService
     * @param string              $secret
     */
    public function __construct(Service\DateService $dateService, $secret = null)
    {
        parent::__construct($dateService, 'X-Api-Client', $secret ?: 'thisIsTheSuperLongSecret@Api2014!');
    }
    /**
     * @param array $parts
     * @param array $options
     */
    protected function processEncoding(array $parts, array $options = [])
    {
        /**
         * Event if the constructor does not require it, the client provider is required.
         * Thanks to a tag (itq.aware.clientprovider) in the container, this service will have it injected.
         *
         * This statement will throw an exception if the client is not found.
         */
        $this->getClientProvider()->get($parts['id'], ['id']);
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
            throw new Exception\MissingClientIdentityException();
        }
        if ($token !== $this->stamp($id, $expire)) {
            throw new Exception\BadClientTokenException();
        }
        if ($this->getDateService()->isDateExpiredFromNow($expire)) {
            throw new Exception\BadClientTokenException();
        }

        return ((array) $this->getClientProvider()->get($parts['id'])) + $parts;
    }
    /**
     * @return array
     */
    protected function getDefaultValues()
    {
        return ['id' => null, 'expire' => null, 'token' => null];
    }
}
