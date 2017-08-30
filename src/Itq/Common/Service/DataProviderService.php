<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Aware\DataProviderAwareInterface;

/**
 * DataProvider Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DataProviderService implements DataProviderAwareInterface
{
    use Traits\ServiceTrait;
    use Traits\PluginAware\DataProviderPluginAwareTrait;
    /**
     * @param string $type
     * @param array  $options
     *
     * @return array
     */
    public function provide($type, array $options = [])
    {
        $data = [];

        foreach ($this->getDataProvidersByType($type) as $dataProvider) {
            $data = array_merge_recursive($data, $dataProvider->provide(['type' => $type] + $options));
        }

        return $data;
    }
}
