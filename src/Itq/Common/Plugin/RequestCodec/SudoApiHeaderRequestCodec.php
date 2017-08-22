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

use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SudoApiHeaderRequestCodec extends Base\AbstractApiHeaderRequestCodec
{
    /**
     * @param Service\DateService $dateService
     */
    public function __construct(Service\DateService $dateService)
    {
        parent::__construct(
            $dateService,
            'X-Api-Sudo',
            [
                'X-Api-Client' => 'auth.header.missing_client',
                'X-Api-User'   => 'auth.header.missing_user',
            ]
        );
    }
}
