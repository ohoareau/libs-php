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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ClientApiHeaderRequestCodec extends Base\AbstractSecretApiHeaderRequestCodec
{
    use Traits\ClientProviderAwareTrait;
    /**
     * @param string $secret
     */
    public function __construct($secret = null)
    {
        parent::__construct('X-Api-Client', $secret ?: 'thisIsTheSuperLongSecret@Api2014!');
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
         */
        $this->getClientProvider()->get($parts['id'], ['id']);
    }
    /**
     * @return array
     */
    protected function getDefaultValues()
    {
        return ['id' => null, 'expire' => null, 'token' => null];
    }
}
