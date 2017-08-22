<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\RequestCodec\Base;

use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractSecretApiHeaderRequestCodec extends AbstractApiHeaderRequestCodec
{
    /**
     * @param Service\DateService $dateService
     * @param string              $headerKey
     * @param string              $secret
     * @param array               $requiredHeaderKeys
     */
    public function __construct(Service\DateService $dateService, $headerKey, $secret, array $requiredHeaderKeys = [])
    {
        parent::__construct($dateService, $headerKey, $requiredHeaderKeys);
        $this->setSecret($secret);
    }
    /**
     * @param string $secret
     *
     * @return $this
     */
    public function setSecret($secret)
    {
        return $this->setParameter('secret', $secret);
    }
    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }
    /**
     * @return string
     */
    protected function getExtraStampedString()
    {
        return $this->getSecret();
    }
}
