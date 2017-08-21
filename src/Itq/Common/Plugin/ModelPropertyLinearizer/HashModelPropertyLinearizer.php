<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelPropertyLinearizer;

use Closure;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class HashModelPropertyLinearizer extends Base\AbstractModelPropertyLinearizer
{
    /**
     * @param array  $data
     * @param string $k
     * @param mixed  $v
     * @param array  $meta
     * @param array  $options
     *
     * @return bool
     */
    public function supports(array &$data, $k, $v, array &$meta, array $options = [])
    {
        return is_array($v) || is_object($v);
    }
    /**
     * @param array   $data
     * @param string  $k
     * @param mixed   $v
     * @param array   $meta
     * @param Closure $objectLinearizer
     * @param array   $options
     */
    public function linearize(array &$data, $k, $v, array &$meta, Closure $objectLinearizer, array $options = [])
    {
        if (is_array($v) && count($v) && !is_numeric(key($v))) {
            $v = (object) $v;
        }

        if (is_object($v)) {
            $objectCast = 'stdClass' === get_class($v);
            $v          = $objectLinearizer($v, $options);
            if (true === $objectCast) {
                $v = (object) $v;
            }
        }

        $data[$k] = $v;
    }
}
