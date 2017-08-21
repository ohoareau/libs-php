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
class GeoPointModelPropertyLinearizer extends Base\AbstractModelPropertyLinearizer
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
        return true === isset($meta['geopoints'][$k]);
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
        foreach ($meta['geopoints'][$k] as $kkk => $vvv) {
            $kk = $meta['geopoints'][$k][$kkk]['name'];
            $geopointConfig = $meta['geopointVirtuals'][$kk];
            $_lat  = (isset($geopointConfig['latitude']) && isset($data[$geopointConfig['latitude']])) ? $data[$geopointConfig['latitude']] : null;
            $_long = (isset($geopointConfig['longitude']) && isset($data[$geopointConfig['longitude']])) ? $data[$geopointConfig['longitude']] : null;
            $data[$kk] = ['type' => 'Point', 'coordinates' => [$_long, $_lat]];
        }
    }
}
