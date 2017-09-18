<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware;

use Itq\Common\Model;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface InstanceChangeAwareInterface
{
    /**
     * @param Model\Internal\Instance $instance
     * @param array                   $options
     *
     * @return void
     */
    public function changeInstance(Model\Internal\Instance $instance, array $options = []);
    /**
     * @param array $options
     *
     * @return void
     */
    public function changeInstanceToDefault(array $options = []);
}
