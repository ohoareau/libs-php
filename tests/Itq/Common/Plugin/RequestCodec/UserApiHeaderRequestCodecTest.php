<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\RequestCodec;

use Itq\Common\Service\UserProviderService;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;
use Itq\Common\Plugin\RequestCodec\UserApiHeaderRequestCodec;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/request-codecs
 * @group plugins/request-codecs/user-api-header
 */
class UserApiHeaderRequestCodecTest extends AbstractPluginTestCase
{
    /**
     * @return UserApiHeaderRequestCodec
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedDateService(), $this->mocked('userProvider', UserProviderService::class), 'thesecret'];
    }
}
