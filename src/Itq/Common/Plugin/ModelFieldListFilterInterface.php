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
 * Model Field List Filter Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelFieldListFilterInterface
{
    /**
     * @param string $model
     * @param array  $fields
     * @param array  $options
     */
    public function filter($model, array &$fields, array $options = []);
}
