<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

/**
 * Arrayizer trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ArrayizerTrait
{
    /**
     * @param mixed    $raw
     * @param null|int $depth
     *
     * @return array
     */
    protected function arrayize($raw, $depth = null)
    {
        if (0 === $depth) {
            return $raw;
        }

        if (null !== $depth) {
            $depth--;
        }

        if (!is_array($raw)) {
            if (is_object($raw)) {
                return $this->arrayize(get_object_vars($raw), $depth);
            }

            return [];
        }

        foreach ($raw as $k => $v) {
            if (is_array($v)) {
                $v = $this->arrayize($v, $depth);
            } elseif (is_object($v)) {
                $v = $this->arrayize(get_object_vars($v), $depth);
            }
            $raw[$k] = $v;
        }

        return $raw;
    }
}
