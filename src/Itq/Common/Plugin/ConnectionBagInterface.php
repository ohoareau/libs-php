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

use Itq\Common\ConnectionInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ConnectionBagInterface
{
    /**
     * @param array $params
     * @param array $options
     *
     * @return ConnectionInterface
     */
    public function getConnection(array $params = [], array $options = []);
}
