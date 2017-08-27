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

use Itq\Common\Aware\ModelFieldListFilterAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelFieldListFilterServiceInterface extends ModelFieldListFilterAwareInterface
{
    /**
     * @param string $model
     * @param array  $fields
     * @param array  $options
     *
     * @return array
     */
    public function prepareFields($model, $fields, array $options = []);
}
