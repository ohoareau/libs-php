<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Helper\Items;

/**
 * Sort trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait SortTrait
{
    /**
     * @param array $items
     * @param array $sorts
     * @param array $options
     *
     * @return $this
     */
    protected function sortItems(&$items, $sorts = [], $options = [])
    {
        if (empty($items)) {
            return $this;
        }

        if (!is_array($sorts)) {
            $sorts = [];
        }

        uasort($items, function ($a, $b) use ($sorts) {
            foreach ($sorts as $field => $direction) {
                if (false === $direction || -1 === (int) $direction || 0 === (int) $direction || 'false' === $direction || null === $direction) {
                    if (!isset($a[$field])) {
                        if (!isset($b[$field])) {
                            continue;
                        } else {
                            return -1;
                        }
                    } elseif (!isset($b[$field])) {
                        continue;
                    }
                    $result = strnatcmp($b[$field], $a[$field]);

                    if ($result > 0) {
                        return $result;
                    }
                } else {
                    if (!isset($a[$field])) {
                        if (!isset($b[$field])) {
                            continue;
                        } else {
                            return 1;
                        }
                    } elseif (!isset($b[$field])) {
                        continue;
                    }
                    $result = strnatcmp($a[$field], $b[$field]);

                    if ($result > 0) {
                        return $result;
                    }
                }
            }

            return -1;
        });

        unset($options);

        return $this;
    }
}
