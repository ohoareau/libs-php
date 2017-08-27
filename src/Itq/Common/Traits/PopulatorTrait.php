<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PopulatorTrait
{
    /**
     * @param array $data
     *
     * @return $this
     */
    protected function populate(array &$data = [])
    {
        foreach ($data as $k => $v) {
            if (!property_exists($this, $k)) {
                continue;
            }

            $this->$k = $v;
        }

        return $this;
    }
}
