<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service\Stub;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class Generator
{
    /**
     * @param $data
     * @return string
     */
    public function fakeGeneratorMethod($data)
    {
        unset($data);

        return 'fake';
    }
}