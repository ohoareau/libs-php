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

use Itq\Common\Aware\ModelUpdateEnricherAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelUpdateEnricherServiceInterface extends ModelUpdateEnricherAwareInterface
{
    /**
     * @param array  $data
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    public function enrichUpdates($data, $class, array $options = []);
}
