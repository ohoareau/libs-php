<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Closure;
use Itq\Common\Aware\ModelPropertyLinearizerAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelPropertyLinearizerServiceInterface extends ModelPropertyLinearizerAwareInterface
{
    /**
     * @param object $doc
     * @param array  $options
     *
     * @return array|object
     */
    public function linearize($doc, $options = []);
    /**
     * @param array   $data
     * @param string  $k
     * @param mixed   $v
     * @param array   $meta
     * @param Closure $objectLinearizer
     * @param array   $options
     *
     * @return void
     */
    public function linearizeProperty(array &$data, $k, $v, array &$meta, Closure $objectLinearizer, array $options = []);
}
