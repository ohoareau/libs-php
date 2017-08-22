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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SudoApiHeaderRequestCodec extends Base\AbstractApiHeaderRequestCodec
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct(
            'X-Api-Sudo',
            [
                'X-Api-Client' => 'auth.header.missing_client',
                'X-Api-User'   => 'auth.header.missing_user',
            ]
        );
    }
}
