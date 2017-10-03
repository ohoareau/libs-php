<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Symfony\Component\HttpFoundation\Request;

/**
 * Request Helper trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait RequestHelperTrait
{
    /**
     * @param array   $params
     * @param Request $request
     *
     * @return array
     */
    public function parseParamsFromRequest(array $params, Request $request)
    {
        foreach ($params as $k => $v) {
            $matches = null;
            if (is_string($v) && 0 < preg_match('/^\%([^\%]+)\%$/', $v, $matches)) {
                $params[$k] = ('query_params' === $matches[1]) ? $request->query->all() : ($request->attributes->has($matches[1]) ? $request->attributes->get($matches[1]) : null);
            }
        }

        return $params;
    }
}
