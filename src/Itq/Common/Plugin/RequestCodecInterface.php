<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

use Symfony\Component\HttpFoundation\Request;

/**
 * Request Codec Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface RequestCodecInterface
{
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return array|null
     */
    public function decode(Request $request, array $options = []);
    /**
     * @param Request $request
     * @param array   $data
     * @param array   $options
     *
     * @return mixed|void
     */
    public function encode(Request $request, array $data = [], array $options = []);
}
