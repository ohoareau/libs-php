<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Service\UserProviderService;
use Itq\Common\Service\TokenProviderService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/request
 */
class RequestServiceTest extends AbstractServiceTestCase
{
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mock('userProvider', UserProviderService::class),
            $this->mock('tokenProvider', TokenProviderService::class),
            'theclientsecret',
            'theusersecret',
        ];
    }
}
