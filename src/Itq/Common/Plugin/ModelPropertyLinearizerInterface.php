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

use Closure;

/**
 * Model Property Linearizer Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelPropertyLinearizerInterface
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
    public function supports(array &$data, $k, $v, array &$meta, array $options = []);
    /**
     * @param array   $data
     * @param string  $k
     * @param mixed   $v
     * @param array   $meta
     * @param Closure $objectLinearizer
     * @param array   $options
     */
    public function linearize(array &$data, $k, $v, array &$meta, Closure $objectLinearizer, array $options = []);
}
