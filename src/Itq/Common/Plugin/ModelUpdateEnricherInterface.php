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

use Exception;
use Itq\Common\ModelInterface;

/**
 * Model Update Enricher Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelUpdateEnricherInterface
{
    /**
     * @param array  $data
     * @param string $k
     * @param mixed  $v
     * @param array  $options
     *
     * @return void
     */
    public function enrich(array &$data, $k, $v, array $options = []);
}
