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

/**
 * ModelDescriptor Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelDescriptorInterface
{
    /**
     * @param string $id
     * @param array  $options
     *
     * @return array
     */
    public function describe($id, array $options = []);
}
