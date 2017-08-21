<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelUpdateEnricher;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ToggleItemsModelUpdateEnricher extends Base\AbstractModelUpdateEnricher
{
    /**
     * @param array  $data
     * @param string $k
     * @param mixed  $v
     * @param array  $options
     *
     * @return void
     */
    public function enrich(array &$data, $k, $v, array $options = [])
    {
        $data[$k.':toggle'] = is_array($v) ? $v : explode(',', $v);
    }
}
