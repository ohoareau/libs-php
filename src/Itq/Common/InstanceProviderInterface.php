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
 * Instance Provider Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface InstanceProviderInterface
{
    /**
     * Return the specified instance.
     *
     * @param string $id
     * @param array  $options
     *
     * @return mixed
     */
    public function load($id, array $options = []);
    /**
     * Clean the specified instance.
     *
     * @param string $id
     * @param array  $options
     *
     * @return mixed
     */
    public function clean($id, array $options = []);
    /**
     * Return the specified instance.
     *
     * @param array $data
     * @param array $options
     *
     * @return array
     */
    public function create(array $data, array $options = []);
}
