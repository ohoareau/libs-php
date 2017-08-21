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
 * Model Cleaner Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelCleanerInterface
{
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return void
     */
    public function clean($doc, array $options = []);
}
