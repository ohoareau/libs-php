<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Poller;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryPoller extends Base\AbstractPoller
{
    /**
     * @return array
     */
    public function poll()
    {
        $available = ['r' => [], 'w' => []];

        foreach ($this->all() as $sourceName => $source) {
            list ($readable, $writable) = $source->testPendings($this->getOption('timeout', -1));
            if (true === $readable) {
                $available['r'][] = $source;
            }
            if (true === $writable) {
                $available['w'][] = $source;
            }
        }

        return $available;
    }
}
