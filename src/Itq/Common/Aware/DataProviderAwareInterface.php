<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware;

use Itq\Common\Plugin;

/**
 * Data Provider Aware Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface DataProviderAwareInterface
{
    /**
     * @param string                       $type
     * @param Plugin\DataProviderInterface $dataProvider
     *
     * @return $this
     */
    public function addDataProvider($type, Plugin\DataProviderInterface $dataProvider);
    /**
     * @return array
     */
    public function getDataProviders();
    /**
     * @param string $type
     *
     * @return Plugin\DataProviderInterface[]
     */
    public function getDataProvidersByType($type);
}
