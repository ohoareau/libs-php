<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * Client Provider Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ClientProviderInterface
{
    /**
     * Return the specified client.
     *
     * @param string $id
     * @param array  $fields
     * @param array  $options
     *
     * @return mixed
     */
    public function get($id, $fields = [], $options = []);
}
