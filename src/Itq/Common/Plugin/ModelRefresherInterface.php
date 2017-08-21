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

use Itq\Common\ModelInterface;

/**
 * Model Refresher Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelRefresherInterface
{
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return ModelInterface
     */
    public function refresh($doc, array $options = []);
}
